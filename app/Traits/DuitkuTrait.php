<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\MlUserInformation;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

trait DuitkuTrait
{
    // example use
    // $productDetails = 'nama product / nama layanan';
    // $itemsDetails = [[
    //     'name' => $subscription->name,
    //     'price' => $subscription->price,
    //     'quantity' => 1,
    // ]];
    // $detailUser = [
    //     'email' => '',
    //     'phone' => '',
    //     'username' => '',
    //     'fullname' => '' 
    // ]
    public function createInvoice($productDetails, $itemDetails, $returnUrl, $paymentFor = '', $detailUser = [], $duitkuMerchantCode = null, $merchantKeyCustom = null)
    {
        $merchantCode = $duitkuMerchantCode ?? env('DUITKU_MERCHANT_CODE');
        $merchantKey = '';
        $merchantHostUrl = '';
        if (env('DUITKU_SANDBOX') === true) {
            $merchantHostUrl = env('DUITKU_HOST_URL_SANDBOX');
            $merchantKey = env('DUITKU_MERCHANT_KEY_SANDBOX');
        } else {
            $merchantHostUrl = env('DUITKU_HOST_URL_PRODUCTION');
            $merchantKey = $merchantKeyCustom ?? env('DUITKU_MERCHANT_KEY_PRODUCTION');
        }
        $timestamp = round(microtime(true) * 1000);
        $paymentAmount = array_reduce($itemDetails, function ($carry, $item) {
            $carry += $item['price'];
            return $carry;
        });
        $merchantOrderId = time() . '';
        $email = $detailUser['email'];
        $phoneNumber = $detailUser['phone'] ?? '';
        $merchantUserInfo = $detailUser['username'];
        $customerVaName = $detailUser['fullname'];
        $callbackUrl = env('DUITKU_CALLBACK_URL');
        $expiryPeriod = 1440;
        $signature = hash('sha256', $merchantCode . $timestamp . $merchantKey);

        $params = array(
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => $expiryPeriod,
            'additionalParam' => $paymentFor
        );

        $params_string = json_encode($params);

        $url = "$merchantHostUrl/api/merchant/createinvoice";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params_string),
                'x-duitku-signature:' . $signature,
                'x-duitku-timestamp:' . $timestamp,
                'x-duitku-merchantcode:' . $merchantCode
            )
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'result' => $httpCode === 200 ? json_decode($request) : $request,
            'httpCode' => $httpCode
        ];
    }

    public function getPaymentUrl($reference)
    {
        $url = 'https://app-sandbox.duitku.com/redirect_checkout';
        if (env('APP_ENV') === 'production')
            $url = 'https://app-prod.duitku.com/redirect_checkout';

        $url = $url . '?reference=' . $reference . '&lang=id';

        return $url;
    }

    public function createQris($productDetails, $itemDetails, $returnUrl, $paymentFor = '', $detailUser = [])
    {
        $merchantCode = env('DUITKU_MERCHANT_CODE');
        $merchantKey = '';
        $merchantHostUrl = '';
        if (env('DUITKU_SANDBOX') === true) {
            $merchantHostUrl = env('DUITKU_HOST_API_URL_SANDBOX');
            $merchantKey = env('DUITKU_MERCHANT_KEY_SANDBOX');
        } else {
            $merchantHostUrl = env('DUITKU_HOST_API_URL_PRODUCTION');
            $merchantKey = env('DUITKU_MERCHANT_KEY_PRODUCTION');
        }
        $timestamp = round(microtime(true) * 1000);
        $paymentAmount = array_reduce($itemDetails, function ($carry, $item) {
            $carry += $item['price'];
            return $carry;
        });
        $merchantOrderId = time() . '';
        $email = $detailUser['email'];
        $phoneNumber = $detailUser['phone'] ?? '';
        $merchantUserInfo = $detailUser['username'];
        $customerVaName = $detailUser['fullname'];
        $callbackUrl = env('DUITKU_CALLBACK_URL');
        $expiryPeriod = 10;
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);

        $params = array(
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => $expiryPeriod,
            'additionalParam' => $paymentFor,

            'merchantCode' => $merchantCode,
            'paymentMethod' => 'SP',
            'signature' => $signature,
        );

        $params_string = json_encode($params);

        $url = "$merchantHostUrl/webapi/api/merchant/v2/inquiry";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params_string)
            )
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return [
            'result' => $httpCode === 200 ? json_decode($request) : $request,
            'httpCode' => $httpCode
        ];
    }
}
