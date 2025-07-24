<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function sendTestEmail()
    {
        $to_email = 'aac.sn11@gmail.com';  // Ganti dengan email penerima
        Mail::raw('This is a test email from Laravel.', function ($message) use ($to_email) {
            $message->to($to_email)
                ->subject('Test Email');
        });

        return 'Test email has been sent.';
    }

    public function sendTestWa()
    {
        $name = "Afif";
        $id = 1;
        $link = 'qweqwe';
        $app_url = str_replace('https://', '', env('APP_URL'));

        $message = "*Yth. Bapak/Ibu $name*\n";
        $message .= "Terima kasih telah bergabung bersama platform akunting terbaik abad ini $app_url \n\n";
        $message .= "Untuk mengaktifkan akun anda silahkan klik link dibawah ini (atau copy paste ke browser anda)\n";
        $message .= url('account_activate') . "?id=$id&code=$link \n\n";
        $message .= "setelah itu gunakan email dan password anda untuk masuk di link bawah ini \n" . url('/frontend_login') . "\n\n";
        $message .= "Salam, \n*Admin Randu.co.id*";

        $params = [
            'phone' => '085736907093',
            'token' => env('WHATSAPP_API_TOKEN'),
            'message' => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://wa.randu.co.id/api/v1/whatsapp/send?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return 'Test whatsapp message has been sent.';
    }
}
