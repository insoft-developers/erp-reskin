<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\JournalReportExport;
use App\Exports\GeneralLedgerExport;
use App\Exports\GeneralLedgerAPIExport;
use App\Exports\TrialExport;
use App\Exports\BalanceExport;
use App\Exports\ProfitLossExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Traits\JournalTrait;
use DateTime;



class ExcelController extends Controller
{
    use JournalTrait;
    public function journal_report_export(Request $request)
    {
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $userid = $this->user_id_staff($date[2]);

        $data = DB::table('ml_journal')->where('userid', $userid)->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->get();
        
        $filename = uniqid().'_journal_report.xlsx';
        
        Excel::store(new JournalReportExport($data, $awal, $akhir), $filename, 'public');
        return response()->json([
            "success" => true,
            "data" => $filename
        ]);
    }

    public function journal_report_pdf(Request $request)
    {
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $userid = $this->user_id_staff($date[2]);

        $data = DB::table('ml_journal')->where('userid', $userid)->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->get();
        
        
        $pdf = Pdf::loadView('export.journal_report', compact('data','awal','akhir'))->setPaper('a4', 'potrait');

        // $path = './storage/app/';
        $fileName = uniqid().'_journal_report.pdf';

        // $pdf->save($path  . $fileName);

        Storage::put('/public/' . $fileName, $pdf->output());

        return response()->json([
            "success" => true,
            "data" => $fileName
        ]);
       
    
    }

    public function general_ledger_export(Request $request)
    {
    
        
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $estimations = $date[2].'_'.$date[3];
        $userid = $this->user_id_staff($date[4]);
        
        $filename = uniqid().'_general_ledger.xlsx';
        
        Excel::store(new GeneralLedgerAPIExport($estimations, $awal, $akhir, $userid), $filename, 'public');


        // return Excel::download(new GeneralLedgerExport($estimations, $awal, $akhir), 'general_ledger.xlsx');
        return response()->json([
            "success" => true,
            "data" => $filename
        ]);
    }

    public function general_ledger_pdf(Request $request)
    {
    
        
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $estimasi = $date[2].'_'.$date[3];
        $userid = $this->user_id_staff($date[4]);
        
       
        
        // Excel::store(new GeneralLedgerAPIExport($estimations, $awal, $akhir, $userid), $filename, 'public');
        $pdf = Pdf::loadView('export.general_ledger2', compact('estimasi','awal','akhir','userid'))->setPaper('a4', 'potrait');

        // $path = './storage/app/';
        $fileName = uniqid().'_general_ledger.pdf';

        // $pdf->save($path  . $fileName);

        Storage::put('/public/' . $fileName, $pdf->output());


        // return Excel::download(new GeneralLedgerExport($estimations, $awal, $akhir), 'general_ledger.xlsx');
        return response()->json([
            "success" => true,
            "data" => $fileName
        ]);
    }

    public function trial_balance_export(Request $request)
    {
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $userid = $this->user_id_staff($date[2]);

        $data = DB::table('ml_journal')->where('userid', $userid)->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->get();

        $dt['current_asset'] = DB::table('ml_current_assets')->where('userid', $userid)->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')->where('userid', $userid)->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')->where('userid', $userid)->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')->where('userid', $userid)->get();

        $dt['income'] = DB::table('ml_income')->where('userid', $userid)->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')->where('userid', $userid)->get();

        $dt['capital'] = DB::table('ml_capital')->where('userid', $userid)->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')->where('userid', $userid)->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $userid)->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')->where('userid', $userid)->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')->where('userid', $userid)->get();
        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')->where('userid', $userid)->get();

        // return Excel::download(new TrialExport($dt, $awal, $akhir, $data), 'trial_balance.xlsx');

        $filename = uniqid().'_trial_balance.xlsx';
        
        Excel::store(new TrialExport($dt, $awal, $akhir, $data), $filename, 'public');


        // return Excel::download(new GeneralLedgerExport($estimations, $awal, $akhir), 'general_ledger.xlsx');
        return response()->json([
            "success" => true,
            "data" => $filename
        ]);
    }


    public function trial_balance_pdf(Request $request)
    {
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $userid = $this->user_id_staff($date[2]);

        $data = DB::table('ml_journal')->where('userid', $userid)->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->get();

        $dt['current_asset'] = DB::table('ml_current_assets')->where('userid', $userid)->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')->where('userid', $userid)->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')->where('userid', $userid)->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')->where('userid', $userid)->get();

        $dt['income'] = DB::table('ml_income')->where('userid', $userid)->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')->where('userid', $userid)->get();

        $dt['capital'] = DB::table('ml_capital')->where('userid', $userid)->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')->where('userid', $userid)->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $userid)->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')->where('userid', $userid)->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')->where('userid', $userid)->get();
        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')->where('userid', $userid)->get();

        // return Excel::download(new TrialExport($dt, $awal, $akhir, $data), 'trial_balance.xlsx');
        // $pdf = Pdf::loadView('export.trial_balance', compact('dt','awal','akhir', 'data'))->setPaper('a4', 'potrait');
 
        // return $pdf->stream('trial_balance.pdf');

        $pdf = Pdf::loadView('export.trial_balance', compact('dt','awal','akhir','data'))->setPaper('a4', 'potrait');

        // $path = './storage/app/';
        $fileName = uniqid().'_trial_balance.pdf';

        // $pdf->save($path  . $fileName);

        Storage::put('/public/' . $fileName, $pdf->output());
        return response()->json([
            "success" => true,
            "data" => $fileName
        ]);
    }

    public function profit_loss_export(Request $request)
    {
        

        $date = explode('_', $request->param);
        

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

        $userid = $this->user_id_staff($date[4]);
        
        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);

           

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

       
       
        $data = $this->list_account($userid);
    
        $filename = uniqid().'_profit_loss.xlsx';
     
        Excel::store(new ProfitLossExport($data, $time_array, $jumlah_periode, $start), $filename, 'public');

        return response()->json([
            "success" => true,
            "data" => $filename
        ]);
    }


    public function profit_loss_pdf(Request $request)
    {
        
        $date = explode('_', $request->param);
        

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

        $userid = $this->user_id_staff($date[4]);
        
        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);

           

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

       
       
        $data = $this->list_account($userid);

        $periode = $jumlah_periode;
        $pdf = Pdf::loadView('export.profit_loss', compact('data', 'time_array', 'periode', 'start'))->setPaper('a4', 'landscape');

        // $path = './storage/app/';
        $fileName = uniqid().'_profit_loss.pdf';

        // $pdf->save($path  . $fileName);

        Storage::put('/public/' . $fileName, $pdf->output());
        return response()->json([
            "success" => true,
            "data" => $fileName
        ]);
    }


    protected function list_account($userid)
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


    public function balance_sheet_export(Request $request)
    {
        $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

        $userid = $this->user_id_staff($date[4]);

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

            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)), $userid);

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

       
        $dt = $this->list_balance_account($userid);
        
        
        $filename = uniqid().'_balance_sheet.xlsx';
     
        Excel::store(new BalanceExport($dt, $start, $time_array, $jumlah_periode, $laba_bersih), $filename, 'public');

        return response()->json([
            "success" => true,
            "data" => $filename
        ]);


    }

    public function balance_sheet_pdf(Request $request)
    {
       $date = explode('_', $request->param);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        $time_array = [];
        $bulan_awal = $start;

        $userid = $this->user_id_staff($date[4]);

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

            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)), $userid);

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

        $dt = $this->list_balance_account($userid);
        $periode = $jumlah_periode;
       
        $pdf = Pdf::loadView('export.balance_sheet', compact('dt', 'start', 'time_array', 'periode', 'laba_bersih'))->setPaper('a4', 'landscape');

        // $path = './storage/app/';
        $fileName = uniqid().'_balance_sheet.pdf';

        // $pdf->save($path  . $fileName);

        Storage::put('/public/' . $fileName, $pdf->output());
        return response()->json([
            "success" => true,
            "data" => $fileName
        ]);
    }

    protected function list_balance_account($userid)
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')->where('userid', $userid)->where('account_code_id', 1)->orderBy('id')->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')->where('userid', $userid)->where('account_code_id', 2)->orderBy('id')->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')->where('userid', $userid)->where('account_code_id', 4)->orderBy('id')->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')->where('userid', $userid)->where('account_code_id', 5)->orderBy('id')->get();
        $data['modal'] = DB::table('ml_capital')->where('userid', $userid)->where('account_code_id', 6)->orderBy('id')->get();
        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')->where('userid', $userid)->where('account_code_id', 3)->orderBy('id')->get();

        return $data;
    }

    protected function count_net_profit($m_from, $y_from, $m_to, $y_to, $userid)
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
}
