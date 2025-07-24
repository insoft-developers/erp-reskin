<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\MlUserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\DuitkuTrait;

class PremiumController extends Controller
{
    use DuitkuTrait;

    public function index()
    {
        $plans = DB::table('subscription_plans')->get()->toArray();
        $data = [];
        foreach ($plans as $plan) {
            $plan->format_price = $plan->price !== 0 ?
                round($plan->price / 1000) . 'k' :
                number_format($plan->price, 0, ',', '.');

            $plan->format_frequency = $plan->frequency === 'Yearly' ? 'Tahun' : 'Bulan';

            $data[strtolower($plan->name)] = $plan;
        }

        $view = "premium";
        return view('main.premium', compact('view', 'data'));
    }

    public function store(Request $request, string $id)
    {
        $subscription = DB::table('subscription_plans')->where('id', $id)->first();
        if ($subscription && $subscription->price !== 0) {
            $accountId = session()->get('id');
            $currentTime = date("Y-m-d H:i:s");
            $existedLog = DB::table('subscription_logs')
                ->where('subscription_id', $id)
                ->where('account_id', $accountId)
                ->where('is_active', 1)
                ->where('status', 0)
                ->where('payment_due_date', '>', $currentTime)
                ->first();

            if ($existedLog) {
                return redirect()->away($this->getPaymentUrl($existedLog->reference));
            } else {
                $productDetails = $subscription->name;
                $itemsDetails = [[
                    'name' => $subscription->name,
                    'price' => $subscription->price,
                    'quantity' => 1,
                ]];

                $currentAccount = Account::where('id', session()->get('id'))->first();
                $detailAccount = MlUserInformation::where('user_id', $currentAccount->id)->first();
                $detailUser = [
                    'email' => $currentAccount->email,
                    'phone' => $detailAccount->phone_number ?? '',
                    'username' => $currentAccount->username,
                    'fullname' => $currentAccount->fullname,
                ];
                $invoice = $this->createInvoice(
                    $productDetails,
                    $itemsDetails,
                    route('premium.index'),
                    'go_premium',
                    $detailUser,
                    env('DUITKU_MERCHANT_CODE_FOR_PREMIUM'),
                    env('DUITKU_MERCHANT_KEY_FOR_PREMIUM_PRODUCTION')
                );
                $result = $invoice['result'];
                if ($invoice['httpCode'] === 200) {
                    $newTimestamp = date("Y-m-d H:i:s", strtotime($currentTime . " +10 minutes"));
                    $log = [
                        'subscription_id' => $id,
                        'account_id' => $accountId,
                        'reference' => $result->reference,
                        'payment_due_date' => $newTimestamp,
                        'payment_return_url' => $result->paymentUrl,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    DB::table('subscription_logs')->insert($log);

                    return redirect()->away($result->paymentUrl);
                }

                return redirect()->back()->withErrors(['message' => $result]);
            }
        }

        return 'Data Not Found';
    }
}
