<?php

namespace App\Http\Controllers;

use App\Mail\ResetAccountMail;
use App\Models\MdProduct;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\WhatsappCrmProvider;
use App\Traits\WhatsappTrait;
use App\Traits\WhatsappTraitPing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DevController extends Controller
{
    use WhatsappTrait, WhatsappTraitPing;

    public function index()
    {
        $user_id = 17593;
        $request = app()->request;
        $staff_id = $request->staff_id;
        $payment_method = $request->payment_method;
        $flag_id = $request->flag_id;

        $query = MdProduct::with(['penjualanProduct.penjualan' => function ($query) use ($flag_id, $staff_id, $payment_method) {
            $query->where('payment_status', 1);
            if ($flag_id) {
                $query->where('flag_id', $flag_id);
            }
            if ($staff_id) {
                $query->where('staff_id', $staff_id);
            }
            if ($payment_method) {
                $query->where('payment_method', $payment_method);
            }
        }]);

        $query = $query
            ->where('user_id', $this->get_branch_id($user_id));

        dd($query->get());
    }
    public function testMail()
    {
        $details = [
            'nama' => 'Afif',
            'email' => 'aac.sn11@mailnesia.com',
            'otp' => '123123',
        ];

        // Mengirim email verifikasi menggunakan Mailable
        Mail::to($details['email'])->send(new ResetAccountMail($details));
        dd('ok');
    }

    public function calcWalletLogs(Request $request)
    {
        $user_id = $request->user_id;
        $email = $request->email;

        if (!$user_id && !$email) {
            return response()->json(['error' => 'User ID or email is required'], 400);
        }

        if ($email) {
            $user = DB::table('ml_accounts')->whereRole_code('general_member')->where('email', $email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user_id = $user->id;
        }

        $walletLogs = DB::table('wallet_logs')
            ->select(
                'wallet_logs.id',
                'wallet_logs.user_id',
                'wallet_logs.amount',
                'wallet_logs.type',
                'wallet_logs.created_at',
                DB::raw('SUM(wallet_logs.amount) OVER (PARTITION BY wallet_logs.user_id ORDER BY wallet_logs.created_at) AS total_balance')
            )
            ->where('wallet_logs.user_id', $user_id)
            ->where('type', '+')
            ->where('status', 3) // Assuming status 3 means completed
            ->sum('wallet_logs.amount');

        $walletLogsMinus = DB::table('wallet_logs')
            ->select(
                'wallet_logs.id',
                'wallet_logs.user_id',
                'wallet_logs.amount',
                'wallet_logs.type',
                'wallet_logs.created_at',
                DB::raw('SUM(wallet_logs.amount) OVER (PARTITION BY wallet_logs.user_id ORDER BY wallet_logs.created_at) AS total_balance')
            )
            ->where('wallet_logs.user_id', $user_id)
            ->where('type', '-')
            ->where('status', 3)
            ->sum('wallet_logs.amount');

        $walletLogsMinusAll = DB::table('wallet_logs')
            ->select(
                'wallet_logs.id',
                'wallet_logs.user_id',
                'wallet_logs.amount',
                'wallet_logs.type',
                'wallet_logs.created_at',
                DB::raw('SUM(wallet_logs.amount) OVER (PARTITION BY wallet_logs.user_id ORDER BY wallet_logs.created_at) AS total_balance')
            )
            ->where('type', '-')
            ->where('status', 3)
            ->sum('wallet_logs.amount');

        return response()->json([
            'user_id' => $user_id,
            'total_income_user' => $walletLogs,
            'admin_fee' => $walletLogsMinus,
            'total_balance' => $walletLogs - $walletLogsMinus,
        ])->setStatusCode(200, 'OK');
    }

    public function testSendMessage(Request $request)
    {
        $phone = $request->input('phone', '6285736907093');
        $message = $request->input('message', 'Test message from DevController');

        try {
            // $this->sendWhatsappMessage($phone, $message);
            $configs = WhatsappCrmProvider::where('owner_id', 17593)
                ->where('is_active', 1)
                ->inRandomOrder()
                ->first();

            if (!$configs) {
                return response()->json(['error' => 'No active WhatsApp provider found'], 404);
            }

            $this->sendWhatsappMessagePing($phone, $message, $configs->credentials['api_key'], $configs->credentials['device_id']);
            return response()->json(['success' => 'Message sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }

    public function devPage(Request $request)
    {
        $reference = $request->get('ref');
        if (!$reference) {
            abort(404, 'Reference not found');
        }

        $data = Penjualan::with(['flag', 'products.variant.variant', 'products.product'])->where('reference', $reference)->first();

        if (!$data) {
            abort(404, 'Data penjualan tidak ditemukan');
        }

        // Mendapatkan informasi toko dari user
        $user = DB::table('ml_accounts')->where('id', $data->user_id)->first();
        $company = DB::table('business_groups')->where('user_id', $data->user_id)->first();

        // Format tanggal
        $tanggal = Carbon::parse($data->created_at)->format('d F Y');

        // Menentukan metode pembayaran
        $metodePembayaran = match ($data->payment_method) {
            'cash' => 'Tunai',
            'randu-wallet' => 'Randu Wallet',
            'qris' => 'QRIS',
            'bank-transfer' => 'Transfer Bank',
            default => ucfirst($data->payment_method)
        };

        // Menentukan status pesanan
        $statusPesanan = match ($data->payment_status) {
            1 => 'Pesanan Selesai',
            0 => 'Pesanan Pending',
            default => 'Status Tidak Diketahui'
        };

        // Menyiapkan item pesanan
        $orderItems = [];
        $subtotal = 0;
        foreach ($data->products as $product) {
            $orderItems[] = [
                'id' => $product->id,
                'name' => $product->product->name,
                'quantity' => $product->quantity,
                'price' => $product->price
            ];
            $subtotal += $product->total;
        }

        // Menghitung total
        $pajak = $data->tax;
        $totalBayar = $data->order_total;
        $uangDiterima = $data->payment_amount;
        $kembalian = $data->payment_return ?? 0;

        $receiptData = [
            'reference' => $data->reference,
            'tanggal' => $tanggal,
            'namaPembeli' => $data->cust_name,
            'noTelepon' => $data->cust_phone ?? '-',
            'metodePembayaran' => $metodePembayaran,
            'noMeja' => $data->qr_codes_id ? "Meja_{$data->qr_codes_id}" : '-',
            'grupMeja' => 'Area Utama',
            'jumlahOrang' => '1',
            'storeName' => $company->branch_name ?? $user->fullname ?? 'Toko',
            'status' => $statusPesanan,
            'idOrder' => $data->reference,
            'noOrder' => $data->id,
            'tipePesanan' => $data->qr_codes_id ? 'Makan Di Tempat' : 'Takeaway',
            'orderItems' => $orderItems,
            'subtotal' => $subtotal,
            'pajak' => $pajak,
            'totalBayar' => $totalBayar,
            'uangDiterima' => $uangDiterima,
            'kembalian' => $kembalian,
            'alamatToko' => $company->business_address ?? 'Alamat tidak tersedia',
            'noTeleponToko' => $user->phone ?? '-',
            'waktuPesan' => Carbon::parse($data->created_at)->format('H:i') . ' WIB',
            'kasir' => $user->fullname ?? 'Kasir',
            'catatan' => $data->note ?? null,
            'paymentStatus' => $data->payment_status
        ];

        return view('receipt', compact('receiptData'));
    }

    public function devPagePending()
    {
        $receiptData = [
            'tanggal' => '13 Juli 2025',
            'namaPembeli' => 'Ida Susanti',
            'noTelepon' => '+6283863314087',
            'metodePembayaran' => 'Tunai',
            'noMeja' => 'Meja_Makan_Kanan',
            'grupMeja' => 'Meja Makan',
            'jumlahOrang' => '3',
            'storeName' => 'Kampoeng Arab Coffee House',
            'status' => 'Pesanan Pending',
            'idOrder' => 'TRI-U73B4LVJNEN',
            'noOrder' => '91',
            'tipePesanan' => 'Makan Di Tempat',
            'orderItems' => [
                [
                    'id' => 1,
                    'name' => 'Paket Seru 2 ORG (Nasi Mandhi Ayam + FREE ICE TEA)',
                    'quantity' => 1,
                    'price' => 70000
                ],
                [
                    'id' => 2,
                    'name' => 'Nasi Kabsah Kambing',
                    'quantity' => 2,
                    'price' => 85000
                ],
                [
                    'id' => 3,
                    'name' => 'Es Teh Manis',
                    'quantity' => 3,
                    'price' => 8000
                ],
                [
                    'id' => 4,
                    'name' => 'Kopi Arab Spesial',
                    'quantity' => 1,
                    'price' => 15000
                ]
            ],
            'subtotal' => 348000,
            'pajak' => 34800,
            'totalBayar' => 382800,
            'uangDiterima' => 400000,
            'kembalian' => 17200,
            'alamatToko' => 'Jl. Raya Serpong No. 123, Tangerang Selatan',
            'noTeleponToko' => '+6221-12345678',
            'waktuPesan' => '14:30 WIB',
            'kasir' => 'Ahmad Fauzi',
            'catatan' => 'Pedas sedang, tanpa bawang'
        ];

        return view('receipt-pending', compact('receiptData'));
    }
}
