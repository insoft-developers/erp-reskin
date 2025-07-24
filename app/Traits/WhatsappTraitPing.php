<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait WhatsappTraitPing
{
    public function sendWhatsappMessagePing(string $phone_number, string $message, $api_key, $device_key)
    {
        // Ubah nomor jika diawali '0' menjadi '62'
        if (substr($phone_number, 0, 1) === '0') {
            $phone_number = '62' . substr($phone_number, 1);
        }

        $url = 'https://chat.ping.co.id/api-app/whatsapp/send-message';
        $payload = [
            "phone" => $phone_number,
            "device_key" => $device_key,
            "api_key" => $api_key,
            "method" => "text",
            "text" => $message,
            "is_group" => false
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        curl_close($ch);
        // Log::info('Whatsapp response', ['response' => $response]);

        $sleepTime = rand(1, 5);
        sleep($sleepTime);
        return $response;
    }
}
