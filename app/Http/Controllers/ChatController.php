<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function countTokens($string)
    {
        // Hapus spasi ekstra di awal atau akhir string
        $string = trim($string);

        // Pisahkan string menjadi kata-kata
        $words = preg_split('/\s+/', $string);

        // Hitung total kata
        $wordCount = count($words);

        // Perkiraan token (1 kata ~ 1,33 token)
        $estimatedTokens = ceil($wordCount * 1.33);

        return $estimatedTokens;
    }

    public function getUSDToIDRRate()
    {
        // Mengambil nilai tukar dari tabel md_currencies untuk USD
        $usdRate = DB::table('md_currencies')
            ->where('code', 'USD')
            ->value('exchange'); // Ambil kolom 'exchange' untuk USD

        return $usdRate;
    }

    public function sendMessage(Request $request)
    {
        // Ambil pesan dari request frontend
        $message = $request->input('message');
        $messageTokens = $this->countTokens($message);

        $files = $request->file('files');

        // Inisialisasi gambar base64 jika ada file gambar
        $base64Images = [];

        if ($files) {
            foreach ($files as $file) {
                // Baca file dan encode dalam base64
                $imageData = base64_encode(file_get_contents($file->getPathname()));

                // Sesuaikan MIME type file, misalnya image/jpeg atau image/png
                $mimeType = $file->getMimeType();

                // Tambahkan format data URI base64 untuk gambar
                $base64Images[] = "data:$mimeType;base64,$imageData";
            }
        }

        $models = DB::table('md_model_ais')->whereIs_active(1)->get();
        $temps = [];
        $counterSystem = 0;
        foreach ($models as $model) {
            $counterSystem++;
            $temps[] = [
                'role' => $model->role,
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $model->content,
                    ],
                ],
            ];
        }

        // Buat pesan payload untuk OpenAI
        $payloadMessages = $temps;

        if ($payloadMessages) {
            $payloadMessages[] = [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $message,
                    ],
                ],
            ];

            // Jika ada gambar, tambahkan ke dalam pesan payload
            foreach ($base64Images as $base64Image) {
                $payloadMessages[$counterSystem]['content'][] = [
                    'type' => 'image_url',
                    'image_url' => ['url' => $base64Image],
                ];
            }
        } else {
            if (count($base64Images)) {
                $payloadMessages[] = [
                    'role' => 'user',
                    'content' => [],
                ];

                // Jika ada gambar, tambahkan ke dalam pesan payload
                foreach ($base64Images as $base64Image) {
                    $payloadMessages[$counterSystem]['content'][] = [
                        'type' => 'image_url',
                        'image_url' => ['url' => $base64Image],
                    ];
                }
            }
        }

        DB::beginTransaction();
        // Kirim pesan dan file (jika ada) ke API OpenAI
        try {
            $conf = DB::table('ml_site_config')->first();
            $max_tokens = $conf->randuai_max_tokens;

            // Biaya per token dalam rupiah
            $costPerTokenIDR = $conf->randuai_fee; // Tarif per token dalam Rupiah, ex: 0.018 rupiah

            $cek = DB::table('ml_accounts')->where('id', session('id'))->first();

            if ($cek->randuai_tokens < $this->countTokens($message) && $cek->balance < $this->countTokens($message) * $costPerTokenIDR) {
                trigger_error('Kuota Gratis Randu AI hari ini sudah habis, silakan Top up saldo Randu Wallet terlebih dahulu untuk terus menggunakan Randu AI');
            }

            // Log::debug($max_tokens);
            // Mengirim pesan dan file ke OpenAI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/chat/completions', [
                // 'model' => 'gpt-4',
                'model' => 'gpt-4o-mini',
                'messages' => $payloadMessages,
                // 'max_tokens' => $max_tokens * 1, // Menambahkan max_tokens
            ]);

            // Ambil pesan balasan dari API OpenAI
            $reply = $response->json('choices.0.message.content');
            $replyTokens = $this->countTokens($reply);

            $totalTokensUsed = $messageTokens + $replyTokens;
            $tokensRemaining = $cek->randuai_tokens;

            // Cek apakah token yang tersisa mencukupi
            if ($tokensRemaining >= $totalTokensUsed) {
                // Jika token mencukupi, hanya kurangi token
                DB::table('ml_accounts')->where('id', session('id'))->update([
                    'randuai_tokens' => $tokensRemaining - $totalTokensUsed,
                    'randuai_tokens_used' => $cek->randuai_tokens_used + $totalTokensUsed,
                ]);
            } else {
                // Jika token tidak cukup, kurangi saldo rupiah
                $deficitTokens = $totalTokensUsed - $tokensRemaining;

                // Hitung biaya kekurangan token dalam rupiah
                $costOfDeficitInIDR = $deficitTokens * $costPerTokenIDR;


                // Periksa apakah saldo mencukupi untuk membayar kekurangan token
                $currentSaldo = $cek->balance; // Saldo dalam rupiah
                if ($currentSaldo >= $costOfDeficitInIDR) {
                    // Update akun dengan saldo dan token yang telah dikurangi
                    DB::table('ml_accounts')->where('id', session('id'))->update([
                        'randuai_tokens' => 0, // Semua token sudah digunakan
                        'randuai_tokens_used' => $cek->randuai_tokens_used + $totalTokensUsed,
                        'balance' => $currentSaldo - $costOfDeficitInIDR, // Kurangi saldo dengan kekurangan token yang dikonversi ke rupiah
                    ]);
                } else {
                    // Jika saldo tidak mencukupi, batalkan transaksi
                    throw new \Exception('Saldo Anda tidak mencukupi untuk melanjutkan aktifitas dengan Randu AI');
                }
            }

            $history = DB::table('ml_ai_chats')->where('conversation_key', $request->conversation_key);
            if (!$history->first()) {
                $headerId = DB::table('ml_ai_chats')->insertGetId([
                    'title' => $message,
                    'user_id' => session('id'),
                    'conversation_key' => $request->conversation_key,
                    'created_at' => now()
                ]);

                DB::table('ml_ai_chat_histories')->insert([
                    'ml_ai_chat_id' => $headerId,
                    'role' => 'user',
                    'content' => $message,
                    'amount' => $this->countTokens($message) * $costPerTokenIDR,
                    'created_at' => now()
                ]);
                DB::table('ml_ai_chat_histories')->insert([
                    'ml_ai_chat_id' => $headerId,
                    'role' => 'system',
                    'content' => $reply,
                    'amount' => $this->countTokens($reply) * $costPerTokenIDR,
                    'created_at' => now()
                ]);
            } else {
                $dt = $history->first();
                DB::table('ml_ai_chat_histories')->insert([
                    'ml_ai_chat_id' => $dt->id,
                    'role' => 'user',
                    'content' => $message,
                    'amount' => $this->countTokens($message) * $costPerTokenIDR,
                    'created_at' => now()
                ]);
                DB::table('ml_ai_chat_histories')->insert([
                    'ml_ai_chat_id' => $dt->id,
                    'role' => 'system',
                    'content' => $reply,
                    'amount' => $this->countTokens($reply) * $costPerTokenIDR,
                    'created_at' => now()
                ]);
            }

            DB::commit();
            // Kembalikan balasan ke frontend
            return response()->json([
                'success'   => true,
                'reply'     => $reply
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error dan kembalikan pesan error ke frontend
            // Log::error('Error saat menghubungi API OpenAI: ' . $e->getMessage());

            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
                // 'Terjadi kesalahan saat menghubungi layanan. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    public function listConversation(Request $request)
    {
        $data = DB::table('ml_ai_chats')->where('user_id', session('id'))->orderBy('id', 'DESC')->get();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function detailConversation(Request $request)
    {
        $key = $request->conversation_key;
        $header = DB::table('ml_ai_chats')->where('conversation_key', $key)->first();

        if ($header) {
            $data = DB::table('ml_ai_chat_histories')->where('ml_ai_chat_id', $header->id)->orderBy('id', 'asc')->get();
            return response()->json([
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'data' => [],
                'message' => 'Data not found'
            ], 200);
        }
    }

    public function newConversation(Request $request)
    {
        $conversation_key = $request->conversation_key;

        DB::beginTransaction(); // Memulai transaksi
        try {
            $data = DB::table('ml_ai_chats')->whereConversation_key($conversation_key)->first();

            if ($data) {
                $details = DB::table('ml_ai_chat_histories')
                    ->where('ml_ai_chat_id', $data->id)
                    ->whereHas_sync(0);

                $total_amount = 0;
                foreach ($details->get() as $dt) {
                    $total_amount += $dt->amount;
                }

                DB::table('wallet_logs')->insert([
                    'user_id' => session('id'),
                    'amount' => $total_amount,
                    'type' => '-',
                    'from' => 'Randu AI',
                    'group' => 'transaction-fee',
                    'note' => 'Transaksi Fee Randu Wallet - Randu AI',
                    'created_at' => now(),
                    'reference' => $conversation_key,
                    'status' => 3,
                ]);

                $details
                    ->update(['has_sync' => 1, 'updated_at' => now()]);
                DB::table('ml_ai_chats')
                    ->whereConversation_key($conversation_key)
                    ->update(['updated_at' => now()]);

                DB::commit(); // Menyimpan perubahan
                return response()->json([
                    'message' => 'Successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Conversation not found',
                ], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Mengembalikan perubahan jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function favConversation(Request $request)
    {
        $data = DB::table('md_model_ai_favorites')
            ->where('is_active', 1)
            ->where('category', $request->query->get('category'))
            ->get();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function favConversationCtg(Request $request)
    {
        $data = DB::table('md_model_ai_favorites')->distinct()->pluck('category');

        return response()->json([
            'data' => $data,
        ]);
    }
}
