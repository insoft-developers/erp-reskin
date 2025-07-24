<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Traits\OmzetTrait;
use App\Traits\PremiumBlockingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BlockingController extends Controller
{
    use PremiumBlockingTrait, OmzetTrait;

    public function check_omset(Request $request) {
        if ($request->has('userid') && $request->has('username')) {
            // Check current route path
            $userid = $this->user_id_manage($request->userid);
            $startOfMonthsAgo = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
            $endOfMonthsAgo = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');


            $user = Account::find($userid);
            
            $summary = $this->get_summary($startOfMonthsAgo, $endOfMonthsAgo, $userid);
            $profit = $this->count_net_profit($startOfMonthsAgo, $endOfMonthsAgo, $userid);
            $nett_profit = $profit['bersih'];
            $gros_profit = $profit['kotor'];
            $validation = $this->get_premium_validation();


            $msg['summary'] = $summary;
            $msg['profit'] = $profit;
            $msg['nett'] = $nett_profit;
            $msg['gross'] = $gros_profit;
            $msg['validation'] = $validation;
           
            if ($nett_profit > $validation->max_net_profit || $gros_profit > $validation->max_gross_profit || $summary > $validation->max_summary) {

               
                                
                if (!$user->is_upgraded) {
                    return response()->json([
                        "success" => false,
                        "message" => "Plese Upgrade",
                        "debug" => $msg,
                    ]);
                } else {
                    return response()->json([
                        "success" => true,
                        "message" => "Go ON",
                        "debug" => $msg,
                    ]);
                }
            }
             return response()->json([
                "success" => true,
                "message" => "GO ON",
                "debug" => $msg,
            ]);
           
        } 
    }

    protected function user_id_manage($userid) {
        $user = \App\Models\MlAccount::findorFail($userid);
        if($user->role_code != 'staff') {
            return $user->id;
        } else {
            $branch = \App\Models\Branch::findorFail($user->branch_id);
            return $branch->account_id;
        }
    }
}
