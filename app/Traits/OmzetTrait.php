<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\MlUserInformation;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait OmzetTrait
{
    public function get_summary(string $startOfMonthsAgo, string $endOfMonthsAgo, int $user_id)
    {
        $users = DB::table('branches as b')
            ->selectRaw('ma.*')
            ->leftJoin('ml_accounts as ma', 'ma.branch_id', '=', 'b.id')
            ->whereAccount_id($user_id)
            ->pluck('id');

        $users[] = $user_id; //user_id untuk role user dan staff jadi 1 disini

        $totalPaid = Penjualan::where(function ($query) {
            $query->where('payment_status', 1)
            ;
        })
            ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
            ->whereIn('user_id', $users)
            ->sum('paid');


        return $totalPaid;
    }

    public function list_account(int $userId = null)
    {
        $data['income'] = DB::table('ml_income')->where('userid', $userId ?? session('id'))->where('account_code_id', 7)->orderBy('id')->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')->where('userid', $userId ?? session('id'))->where('account_code_id', 8)->orderBy('id')->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $userId ?? session('id'))->where('account_code_id', 9)->orderBy('id')->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')->where('userid', $userId ?? session('id'))->where('account_code_id', 10)->orderBy('id')->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')->where('userid', $userId ?? session('id'))->where('account_code_id', 11)->orderBy('id')->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')->where('userid', $userId ?? session('id'))->where('account_code_id', 12)->orderBy('id')->get();

        return $data;
    }

    /**
     * startOfMonthsAgo = '2024-07-23 13:44:99
     */
    public function count_net_profit(string $startOfMonthsAgo, string $endOfMonthsAgo, int $userId = null)
    {
        $startOfMonthsAgo = Carbon::createFromFormat('Y-m-d H:i:s', $startOfMonthsAgo)->timestamp;
        $endOfMonthsAgo = Carbon::createFromFormat('Y-m-d H:i:s', $endOfMonthsAgo)->timestamp;

        $data = $this->list_account($userId);
        $total_income = 0;

        foreach ($data['income'] as $i) {

            $income = DB::table('ml_journal_list')
                ->where('asset_data_id', $i->id)
                ->where('account_code_id', 7)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('credit - debet'));
            $total_income = $total_income + $income;
        }

        $total_hpp = 0;

        foreach ($data['hpp'] as $a) {

            $hpp = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 8)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('debet-credit'));
            $total_hpp = $total_hpp + $hpp;
        }

        $laba_rugi_kotor = $total_income - $total_hpp;
        $total_selling_cost = 0;

        foreach ($data['selling_cost'] as $a) {

            $selling_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 9)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('debet-credit'));
            $total_selling_cost = $total_selling_cost + $selling_cost;
        }

        $total_general_fees = 0;
        foreach ($data['general_fees'] as $a) {
            $general_fees = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 10)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('debet-credit'));
            $total_general_fees = $total_general_fees + $general_fees;
        }

        $total_nb_income = 0;

        foreach ($data['non_business_income'] as $a) {

            $nb_income = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 11)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('credit-debet'));
            $total_nb_income = $total_nb_income + $nb_income;
        }

        $total_nb_cost = 0;
        foreach ($data['non_business_cost'] as $a) {
            $nb_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 12)
                ->whereBetween('created', [$startOfMonthsAgo, $endOfMonthsAgo])
                ->sum(DB::raw('debet-credit'));
            $total_nb_cost = $total_nb_cost + $nb_cost;
        }

        $laba_bersih = $laba_rugi_kotor - $total_selling_cost - $total_general_fees + $total_nb_income - $total_nb_cost;


        return ['bersih' => $laba_bersih, 'kotor' => $laba_rugi_kotor];
    }
}
