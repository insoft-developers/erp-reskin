<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait WhatsappTrait
{
    public function sendWhatsappMessage(string $phone_number, string $message)
    {
        // Check if we should use iMessage API instead
        if (env('IMESSAGE_ACTIVE') == true) {
            return $this->sendIMessageMessage($phone_number, $message);
        }

        // Default WhatsApp implementation
        $params = [
            'phone' => $phone_number,
            'token' => env('WHATSAPP_API_TOKEN'),
            'message' => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://wa.randu.co.id/api/v1/whatsapp/send?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        $sleepTime = rand(1, 5);
        sleep($sleepTime);
        return 'ok';
    }

    private function sendIMessageMessage(string $phone_number, string $message)
    {
        // Log::debug('Sending iMessage message', [
        //     'phone_number' => $phone_number,
        //     'message' => $message
        // ]);


        // Check if the phone number starts with '0' and replace it with '62'
        if (substr($phone_number, 0, 1) === '0') {
            $phone_number = '62' . substr($phone_number, 1);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://imessage.id/api/create-message');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $config = DB::table('ml_site_config')
            ->first();

        $imessage_app_key = $config->imessage_app_key;
        $imessage_auth_key = $config->imessage_auth_key;

        $postFields = [
            'appkey' => $imessage_app_key,
            'authkey' => $imessage_auth_key,
            'to' => $phone_number,
            'message' => $message
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        // Log::debug('iMessage response', [
        //     'response' => $response
        // ]);
        curl_close($ch);

        $sleepTime = rand(1, 5);
        sleep($sleepTime);
        return 'ok';
    }
}
