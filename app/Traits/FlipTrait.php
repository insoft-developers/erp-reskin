<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\MlUserInformation;
use Illuminate\Support\Carbon;

trait FlipTrait
{
    protected $flip_secret_key;
    protected $flip_host_url;

    public function __construct()
    {
        if (env('FLIP_SANDBOX') === 'true') {
            $this->flip_secret_key = env('FLIP_SECRET_KEY_SANDBOX');
            $this->flip_host_url = env('FLIP_HOST_URL_SANDBOX');
        } else {
            $this->flip_secret_key = env('FLIP_SECRET_KEY_PRODUCTION');
            $this->flip_host_url = env('FLIP_HOST_URL_PRODUCTION');
        }
    }

    public function createDisbursement(int $account_number, string $bank_code, int $amount, string $remark, string $email, string $reference)
    {
        $flip_secret_key = '';
        $flip_host_url = '';
        if (env('FLIP_SANDBOX') === true) {
            $flip_secret_key = env('FLIP_SECRET_KEY_SANDBOX');
            $flip_host_url = env('FLIP_HOST_URL_SANDBOX');
        } else {
            $flip_secret_key = env('FLIP_SECRET_KEY_PRODUCTION');
            $flip_host_url = env('FLIP_HOST_URL_PRODUCTION');
        }

        $payloads = [
            "account_number" => $account_number,
            "bank_code" => $bank_code,
            "amount" => $amount,
            "remark" => $remark,
            "beneficiary_email" => $email
        ];

        $currentDateTime = Carbon::now()->setTimezone('Asia/Jakarta');
        $formattedDateTime = $currentDateTime->format('Y-m-d\TH:i:sO');
        $idempotencyKey = $reference;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$flip_host_url}/v3/disbursement");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded",
            "idempotency-key: $idempotencyKey", // ini bisa di isi id transaksi
            "X-TIMESTAMP: $formattedDateTime"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $flip_secret_key . ":");

        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return [
            'result' => $httpCode === 200 ? json_decode($request) : $request,
            'httpCode' => $httpCode
        ];
    }

    public function bankAccountInquiry(int $account_number, string $bank_code, string $inquiry_key)
    {
        $ch = curl_init();
        $secret_key = $this->flip_secret_key;

        curl_setopt($ch, CURLOPT_URL, "{$this->flip_host_url}/api/v2/disbursement/bank-account-inquiry");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "account_number=$account_number&bank_code=$bank_code&inquiry_key=$inquiry_key");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/x-www-form-urlencoded"
        ));

        curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");

        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return [
            'result' => $httpCode === 200 ? json_decode($request) : $request,
            'httpCode' => $httpCode
        ];
    }
}
