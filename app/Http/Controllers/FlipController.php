<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\MlGaji;
use App\Models\WalletLogs;
use App\Traits\WhatsappTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlipController extends Controller
{
    use WhatsappTrait;
    protected $token_validation;

    public function __construct()
    {
        if (env('FLIP_SANDBOX') === true) {
            $this->token_validation = env('FLIP_TOKEN_VALIDATION_SANDBOX');
        } else {
            $this->token_validation = env('FLIP_TOKEN_VALIDATION_PRODUCTION');
        }
    }

    public function callback(Request $request)
    {
        // Log::debug($request->all());
        $data = json_decode($request->data);
        $token_validation = $this->token_validation;

        if ($request->token === $token_validation) {
            $reference = $data->idempotency_key;

            if ($data->remark === 'user-withdraw') {
                $this->callBackUserWithdraw($reference);
            } else if (strpos($data->remark, 'Gaji') !== false) {
                $this->callBackGajiMarketing($reference);
            }
        }
        return 'ok';
    }

    public function callbackInquiry(Request $request)
    {
        // Log::debug($request->all());

        // untuk debug callback duitku dan perlu di filter dulu $request->additionalParam sesuai kebutuhan
        // $debug_send_callback_to_wa = ['085736907093', '081253433043'];
        // foreach ($debug_send_callback_to_wa as $phone) {
        //     $this->sendWhatsappMessage($phone, json_encode($request->all()));
        // }


        $data = json_decode($request->data);
        $inquiry_key = $data->inquiry_key;
        $token = $request->token;
        $token_validation = $this->token_validation;

        if ($token === $token_validation) {
            $string = $inquiry_key;
            $parts = explode('-', $string);
            $table_name = $parts[0];
            $status = $data->status;
            if ($table_name === 'ml_marketings') {
                $table_id = $parts[1];
                $query = DB::table($table_name)->whereId($table_id)->first();
                $bankAccount = json_decode($query->bank_account, true);
                $bankAccount['status'] = $status;
                $action = DB::table($table_name)->whereId($table_id)->update([
                    'bank_account' => json_encode($bankAccount),
                ]);
            }
        }
    }

    public function callBackUserWithdraw(string $reference)
    {
        WalletLogs::whereReference($reference)->whereGroup('withdraw')->update(['status' => 3, 'payment_at' => now()]);
        WalletLogs::whereReference($reference)->whereGroup('withdraw-fee')->update(['status' => 3]);

        // $currWallLog = WalletLogs::whereReference($reference)->whereGroup('withdraw')->first();
        // Account::whereId($currWallLog->user_id)->decrement('balance', $currWallLog->amount);
        // $currWallLog = WalletLogs::whereReference($reference)->whereGroup('withdraw-fee')->first();
        // Account::whereId($currWallLog->user_id)->decrement('balance', $currWallLog->amount);
    }

    public function callBackGajiMarketing(string $reference)
    {
        MlGaji::whereReference($reference)->update(['transfered_status' => 2]);
    }
}
