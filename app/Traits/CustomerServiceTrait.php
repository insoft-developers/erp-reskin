<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendWhatsappMessageService;
use Illuminate\Support\Facades\Log;

trait CustomerServiceTrait
{
    private $host = 'https://imessage.id/api/randu';

    public function getCustomerServiceId($userId)
    {
        // Cek apakah user ini memiliki data di md_customer_services
        $csList = DB::table('md_customer_services')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->orderBy('id', 'asc')
            ->pluck('id'); // Ambil daftar cs_id untuk user ini

        if ($csList->isEmpty()) {
            // Jika tidak ada customer service untuk user ini, kembalikan null atau default value
            return null; // atau bisa diganti dengan tindakan lain yang sesuai
        }

        // Ambil id terakhir dari penjualan yang menggunakan customer service milik user ini
        $lastCSId = DB::table('penjualan')
            ->where('user_id', $userId)
            ->whereIn('cs_id', $csList)
            ->orderBy('id', 'desc')
            ->value('cs_id');

        // Jika belum ada penjualan, kembalikan cs_id pertama dari daftar user ini
        if (!$lastCSId) {
            return $csList->first();
        }

        $nextCSId = $csList->first(function ($id) use ($lastCSId) {
            return $id > $lastCSId;
        });

        if (!$nextCSId) {
            // Jika tidak ada customer service berikutnya dalam daftar, kembali ke yang pertama
            $nextCSId = $csList->first();
        }

        return $nextCSId;
    }

    public function formatPhoneNumber($phone)
    {
        // Hapus semua karakter kecuali angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika nomor dimulai dengan '08', ubah menjadi '62'
        if (substr($phone, 0, 2) === '08') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika nomor dimulai dengan '062', ubah menjadi '62'
        if (substr($phone, 0, 3) === '062') {
            $phone = '62' . substr($phone, 3);
        }

        // Cek apakah string diawali dengan '62', jika tidak, tambahkan '62' di depan string
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    public function sendMessage(string $message, $phoneNumber, string $appKey, string $authKey)
    {
        // Log::debug($phoneNumber);
        if ($phoneNumber) {
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            SendWhatsappMessageService::dispatch($message, $phoneNumber, $appKey, $authKey)->onQueue('cs_send_message');
        }
    }

    public function customerServiceSendMessage($penjualanId)
    {
        $penjualan = DB::table('penjualan as main')
            ->selectRaw('user.email as user_email, main.cust_name, main.cust_phone, main.cs_id, main.reference, cust.name as custin_name, cust.phone as custin_phone, product.name as productname, cs.name as cs_name, cs.appkey, cs_template.*')
            ->leftJoin('ml_accounts as user', 'user.id', '=', 'main.user_id')
            ->leftJoin('penjualan_products as d', 'd.penjualan_id', '=', 'main.id')
            ->leftJoin('md_products as product', 'product.id', '=', 'd.product_id')
            ->leftJoin('md_customers as cust', 'cust.id', '=', 'main.customer_id')
            ->leftJoin('md_customer_services as cs', 'cs.id', '=', 'main.cs_id')
            ->leftJoin('md_customer_service_message_templates as cs_template', 'cs_template.cs_id', '=', 'cs.id')
            ->where('main.id', $penjualanId)
            ->where('cs.is_active', 1)
            ->first();

        if ($penjualan) {
            $res = Http::get($this->host . '/user', [
                'email' => $penjualan->user_email
            ]);
            $authKey = $res->json()['authkey'];
            $appKey = $penjualan->appkey;

            $data = [
                'productname' => $penjualan->productname,
                'customer_name' => $penjualan->custin_name ?? $penjualan->cust_name,
                'customer_phone' => $penjualan->custin_phone ?? $penjualan->cust_phone,
                'customerservice' => $penjualan->cs_name,
                'link_struk' => env('APP_URL') . '/pos/print-receipt?reference=' . $penjualan->reference
            ];

            $templates_master = DB::table('md_message_templates')->get();
            foreach ($templates_master as $key => $item) {
                $template = $penjualan->{"template_" . $item->key} ?? $item->template;
                // Replace placeholders in the template
                foreach ($data as $placeholder => $value) {
                    $template = str_replace('{' . $placeholder . '}', $value, $template);
                }

                // send disini
                $this->sendMessage($template, $data['customer_phone'], $appKey, $authKey);
            }
        }
    }
}
