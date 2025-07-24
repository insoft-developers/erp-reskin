<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Traits\CommonTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebviewController extends Controller
{
    use CommonTrait;
    public function neraca($userid, $fmonth, $fyear, $tmonth, $tyear)
    {
        
       
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $tmonth, $tyear);

        $start = $fyear . '-' . $fmonth . '-01';
        $end = $tyear . '-' . $tmonth . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

        $laba_bersih = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($laba_bersih, 0);
        }

       

        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);

            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)), $this->user_id_manage($userid));

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }



        $dt = $this->list_balance_account($this->user_id_manage($userid));
        
        $periode = $jumlah_periode;
        return view('webview.neraca', compact('dt', 'start', 'time_array', 'periode', 'laba_bersih'));
    }

    public function count_net_profit($m_from, $y_from, $m_to, $y_to, $userid)
    {
        $start = $y_from . '-' . $m_from . '-01';

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $m_to, $y_to);
        $end = $y_to . '-' . $m_to . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = $this->list_account($userid);
        $total_income = 0;

        foreach ($data['income'] as $i) {
            $income = DB::table('ml_journal_list')
                ->where('asset_data_id', $i->id)
                ->where('account_code_id', 7)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));
            $total_income = $total_income + $income;
        }

        $total_hpp = 0;

        foreach ($data['hpp'] as $a) {
            $hpp = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 8)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_hpp = $total_hpp + $hpp;
        }

        $laba_rugi_kotor = $total_income - $total_hpp;
        $total_selling_cost = 0;

        foreach ($data['selling_cost'] as $a) {
            $selling_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 9)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_selling_cost = $total_selling_cost + $selling_cost;
        }

        $total_general_fees = 0;
        foreach ($data['general_fees'] as $a) {
            $general_fees = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 10)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_general_fees = $total_general_fees + $general_fees;
        }

        $total_nb_income = 0;

        foreach ($data['non_business_income'] as $a) {
            $nb_income = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 11)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_nb_income = $total_nb_income + $nb_income;
        }

        $total_nb_cost = 0;
        foreach ($data['non_business_cost'] as $a) {
            $nb_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 12)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_nb_cost = $total_nb_cost + $nb_cost;
        }

        $laba_bersih = $laba_rugi_kotor - $total_selling_cost - $total_general_fees + $total_nb_income - $total_nb_cost;

        return $laba_bersih;
    }

    public function list_account($userid)
    {
        $data['income'] = DB::table('ml_income')
            ->where('userid', $userid)
            ->where('account_code_id', 7)
            ->orderBy('id')
            ->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')
            ->where('userid', $userid)
            ->where('account_code_id', 8)
            ->orderBy('id')
            ->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $userid)
            ->where('account_code_id', 9)
            ->orderBy('id')
            ->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')
            ->where('userid', $userid)
            ->where('account_code_id', 10)
            ->orderBy('id')
            ->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')
            ->where('userid', $userid)
            ->where('account_code_id', 11)
            ->orderBy('id')
            ->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $userid)
            ->where('account_code_id', 12)
            ->orderBy('id')
            ->get();

        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $userid)
            ->where('account_code_id', 3)
            ->orderBy('id')
            ->get();

        return $data;
    }

    public function list_balance_account($userid)
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')
            ->where('userid', $userid)
            ->where('account_code_id', 1)
            ->orderBy('id')
            ->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')
            ->where('userid', $userid)
            ->where('account_code_id', 2)
            ->orderBy('id')
            ->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')
            ->where('userid', $userid)
            ->where('account_code_id', 4)
            ->orderBy('id')
            ->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')
            ->where('userid', $userid)
            ->where('account_code_id', 5)
            ->orderBy('id')
            ->get();
        $data['modal'] = DB::table('ml_capital')
            ->where('userid', $userid)
            ->where('account_code_id', 6)
            ->orderBy('id')
            ->get();
        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $userid)
            ->where('account_code_id', 3)
            ->orderBy('id')
            ->get();

        return $data;
    }

    public function profit_loss($userid, $fmonth, $fyear, $tmonth, $tyear)
    {
        

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $tmonth, $tyear);

        $start = $fyear . '-' . $fmonth . '-01';
        $end = $tyear . '-' . $tmonth . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

       

       
        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);

           

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

       

        $data = $this->list_account($this->user_id_manage($userid));
        $periode = $jumlah_periode;
        return view('webview.profit_loss', compact('data', 'time_array', 'periode', 'start'));
    
    }

}
