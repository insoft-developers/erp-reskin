<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

use App\Exports\ProfitLossExport;
use App\Exports\BalanceExport;
use App\Exports\GeneralLedgerExport;
use App\Exports\JournalReportExport;
use App\Exports\TrialExport;
use App\Models\Branch;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MlAccount;
use App\Traits\CommonTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'report';
        return view('main.report', compact('view'));
    }

    public function list_account()
    {
        $data['income'] = DB::table('ml_income')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 7)
            ->orderBy('id')
            ->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 8)
            ->orderBy('id')
            ->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 9)
            ->orderBy('id')
            ->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 10)
            ->orderBy('id')
            ->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 11)
            ->orderBy('id')
            ->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 12)
            ->orderBy('id')
            ->get();

        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $userId ?? $this->user_id_manage(session('id')))
            ->where('account_code_id', 3)
            ->orderBy('id')
            ->get();

        return $data;
    }

    public function list_balance_account()
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 1)
            ->orderBy('id')
            ->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 2)
            ->orderBy('id')
            ->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 4)
            ->orderBy('id')
            ->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 5)
            ->orderBy('id')
            ->get();
        $data['modal'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 6)
            ->orderBy('id')
            ->get();
        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('account_code_id', 3)
            ->orderBy('id')
            ->get();

        return $data;
    }

    public function profit_loss(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'profit-loss';
        $data = $this->list_account();
        return view('main.profit_loss_report', compact('view', 'data', 'userKey', 'from'));
    }

    public function submit_profit_loss(Request $request)
    {
        $input = $request->all();

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['month_to'], $input['year_to']);

        $start = $input['year_from'] . '-' . $input['month_from'] . '-01';
        $end = $input['year_to'] . '-' . $input['month_to'] . '-' . $tanggal_akhir;

        if ($start > $end) {
            return response()->json([
                'success' => false,
                'message' => 'Period To can not bigger than Period From',
            ]);
        }

        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        // =============

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

        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = $this->list_account();

        $kolspan1 = 3 + $jumlah_periode * 2;

        $html = '';
        $html .= '<table class="table table-bordered" id="table-profit-loss">';
        $html .= '<tr>';
        $html .= '<th rowspan="2"><center>Keterangan</center></th>';
        $display_month = $start;
        for ($i = 0; $i <= $jumlah_periode; $i++) {
            $html .= '<th colspan="2"><center>' . date('F Y', strtotime($display_month)) . '</center></th>';
            $display_month = date('Y-m-d', strtotime($display_month . ' + 1 month'));
        }
        $html .= '</tr>';
        $html .= '<tr>';
        for ($i = 0; $i <= $jumlah_periode; $i++) {
            $html .= '<th>*</th>';
            $html .= '<th>*</th>';
        }
        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '" style="border-top:2px solid black;"><strong>Pendapatan</strong></td></tr>';

        $total_income = [];
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_income, 0);
        }

        foreach ($data['income'] as $i) {
            $_income = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $income = DB::table('ml_journal_list')
                    ->where('asset_data_id', $i->id)
                    ->where('account_code_id', 7)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit - debet'));
                $total_income[$in] = $total_income[$in] + $income;
                $_income = $_income + $income;
                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $i->name . '</td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($income) . '</td>';
                $html .= '<td></td>';
            }
            if ($_income === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $html .= '<tr>';
        $html .= '<td><strong>Pendapatan Bersih</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">' . number_format($total_income[$in]) . '</td>';
        }
        $html .= '</tr>';

        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Harga Pokok Penjualan</strong></td></tr>';

        $total_hpp = [];

        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_hpp, 0);
        }

        foreach ($data['hpp'] as $a) {
            $_hpp = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $hpp = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 8)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet-credit'));
                $total_hpp[$in] = $total_hpp[$in] + $hpp;
                $_hpp = $_hpp + $hpp;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $a->name . '</td>';
                }

                $html .= '<td style="text-align:right;">(' . number_format($hpp) . ')</td>';
                $html .= '<td></td>';
            }
            if ($_hpp === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $html .= '<tr>';
        $html .= '<td><strong>Total Harga Pokok Penjualan</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">(' . number_format($total_hpp[$in]) . ')</td>';
        }
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td><strong>LABA/RUGI KOTOR</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($total_income[$in] - $total_hpp[$in]) . '</strong></td>';
        }
        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Biaya Penjualan</strong></td></tr>';

        $total_selling_cost = [];
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_selling_cost, 0);
        }

        foreach ($data['selling_cost'] as $a) {
            $_selling_cost = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $selling_cost = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 9)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet-credit'));
                $total_selling_cost[$in] = $total_selling_cost[$in] + $selling_cost;
                $_selling_cost = $_selling_cost + $selling_cost;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $a->name . '</td>';
                }

                $html .= '<td style="text-align:right;">(' . number_format($selling_cost) . ')</td>';
                $html .= '<td></td>';
            }
            if ($_selling_cost === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td><strong>Total Biaya Penjualan</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">(' . number_format($total_selling_cost[$in]) . ')</td>';
        }
        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Biaya Umum Admin</strong></td></tr>';

        $total_general_fees = [];
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_general_fees, 0);
        }

        foreach ($data['general_fees'] as $a) {
            $_gm = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $general_fees = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 10)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet-credit'));
                $total_general_fees[$in] = $total_general_fees[$in] + $general_fees;
                $_gm = $_gm + $general_fees;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $a->name . '</td>';
                }

                $html .= '<td style="text-align:right;">(' . number_format($general_fees) . ')</td>';
                $html .= '<td></td>';
            }
            if ($_gm === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $html .= '<tr>';
        $html .= '<td><strong>Total Biaya Admin dan Umum</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">(' . number_format($total_general_fees[$in]) . ')</td>';
        }
        $html .= '</tr>';

        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Pendapatan Diluar Usaha</strong></td></tr>';

        $total_nb_income = [];
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_nb_income, 0);
        }

        foreach ($data['non_business_income'] as $a) {
            $_nbcome = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $nb_income = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 11)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit-debet'));
                $total_nb_income[$in] = $total_nb_income[$in] + $nb_income;
                $_nbcome = $_nbcome + $nb_income;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $a->name . '</td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($nb_income) . '</td>';
                $html .= '<td></td>';
            }
            if ($_nbcome === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td><strong>Total Pendapatan Diluar Usaha</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">' . number_format($total_nb_income[$in]) . '</td>';
        }
        $html .= '</tr>';

        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Biaya Diluar Usaha</strong></td></tr>';

        $total_nb_cost = [];
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            array_push($total_nb_cost, 0);
        }

        foreach ($data['non_business_cost'] as $a) {
            $_nbcost = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $nb_cost = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 12)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet-credit'));
                $total_nb_cost[$in] = $total_nb_cost[$in] + $nb_cost;
                $_nbcost = $_nbcost + $nb_cost;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp;' . $a->name . '</td>';
                }

                $html .= '<td style="text-align:right;">(' . number_format($nb_cost) . ')</td>';
                $html .= '<td></td>';
            }

            if ($_nbcost === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td><strong>Total Biaya Diluar Usaha</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">(' . number_format($total_nb_cost[$in]) . ')</td>';
        }
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td><strong>LABA/RUGI BERSIH</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"><strong>' . number_format($total_income[$in] - $total_hpp[$in] - $total_selling_cost[$in] - $total_general_fees[$in] + $total_nb_income[$in] - $total_nb_cost[$in]) . '</strong></td>';
        }
        $html .= '</tr>';
        $html .= '</table>';

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function network() {}

    public function balance(Request $request)
    {
        $userKey = $request->user_key ?? null;

        $from = $request->from ?? 'desktop';
        $view = 'balance-sheet';
        $data = $this->list_account();
        $dt = $this->list_balance_account();
        $laba_bersih = $this->count_net_profit(date('m'), date('Y'), date('m'), date('Y'));
        return view('main.balance_sheet', compact('view', 'data', 'dt', 'laba_bersih', 'userKey', 'from'));
    }

    public function count_net_profit($m_from, $y_from, $m_to, $y_to)
    {
        $start = $y_from . '-' . $m_from . '-01';

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $m_to, $y_to);
        $end = $y_to . '-' . $m_to . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = $this->list_account();
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

    public function submit_balance_sheet(Request $request)
    {
        $input = $request->all();

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['month_to'], $input['year_to']);

        $start = $input['year_from'] . '-' . $input['month_from'] . '-01';
        $end = $input['year_to'] . '-' . $input['month_to'] . '-' . $tanggal_akhir;

        if ($start > $end) {
            return response()->json([
                'success' => false,
                'message' => 'Period To can not bigger than Period From',
            ]);
        }

        // hitung
        $_start = new DateTime($start);
        $_akhir = new DateTime($end);

        $jumlah_tahun = $_start->diff($_akhir)->y;
        $jumlah_bulan = $_start->diff($_akhir)->m;

        $jumlah_periode = $jumlah_tahun * 12 + $jumlah_bulan;

        // =============

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

            $awal = strtotime($bulan_pertama);
            $akhir = strtotime($bulan_kedua);

            $custom_date = date('Y-m', strtotime($awal));

            $get_first_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' first day of previous month'));
            $get_last_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' last day of previous month'));

            $fm = date('m', strtotime($get_first_day_of_prev_month));
            $fy = date('Y', strtotime($get_first_day_of_prev_month));

            $tm = date('m', strtotime($get_last_day_of_prev_month));
            $ty = date('Y', strtotime($get_last_day_of_prev_month));

            // $u_from = strtotime($get_first_day_of_prev_month);
            // $u_to = strtotime($get_last_day_of_prev_month);

            $this->opening_balance(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)));

            $dt = $this->list_balance_account();
            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)));
            $prev_profit = $this->count_net_profit($fm, $fy, $tm, $ty);

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

        $html = '';
        $html .= '<table class="table table-bordered" id="table-profit-loss">';
        $html .= '<tr>';
        $html .= '<th rowspan="2"><center>Keterangan</center></th>';

        $display_month = $start;
        for ($i = 0; $i <= $jumlah_periode; $i++) {
            $html .= '<th colspan="2"><center>' . date('F Y', strtotime($display_month)) . '</center></th>';
            $display_month = date('Y-m-d', strtotime($display_month . ' + 1 month'));
        }
        $html .= '</tr>';

        $html .= '<tr>';
        for ($i = 0; $i <= $jumlah_periode; $i++) {
            $html .= '<th>*</th>';
            $html .= '<th>*</th>';
        }
        $html .= '</tr>';

        $kolspan1 = 3 + 2 * $jumlah_periode;
        $html .= '<tr>';

        $html .= '<td colspan="' . $kolspan1 . '" style="border-top:2px solid black;"><strong>Aktiva Lancar</strong></td>';

        $html .= '</tr>';

        $total_lancar = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_lancar, 0);
        }

        foreach ($dt['aktiva_lancar'] as $ndex => $i) {
            $tlancar = 0;
            $html .= '<tr>';

            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $lancar = DB::table('ml_journal_list')
                    ->where('asset_data_id', $i->id)
                    ->where('account_code_id', 1)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet - credit'));
                $total_lancar[$in] = $total_lancar[$in] + $lancar;
                $tlancar = $tlancar + $lancar;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $i->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($lancar) . '</td>';
                $html .= '<td></td>';
            }

            if ($tlancar === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        // dd($total_lancar);

        $html .= '<tr>';

        $html .= '<td><strong>Total Aktiva Lancar</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"> ' . number_format($total_lancar[$in]) . ' </td>';
        }

        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Aktiva Tetap</strong></td></tr>';

        $total_tetap = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_tetap, 0);
        }

        foreach ($dt['aktiva_tetap'] as $a) {
            $_tetap = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $tetap = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 2)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('debet-credit'));
                $total_tetap[$in] = $total_tetap[$in] + $tetap;
                $_tetap = $_tetap + $tetap;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $a->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($tetap) . ' </td>';
                $html .= '<td></td>';
            }
            if ($_tetap === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $total_akumulasi = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_akumulasi, 0);
        }

        foreach ($dt['akumulasi'] as $a) {
            $_akumulasi = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $akumulasi = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 3)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit-debet'));
                $total_akumulasi[$in] = $total_akumulasi[$in] + $akumulasi;
                $_akumulasi = $_akumulasi + $akumulasi;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $a->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">(' . number_format($akumulasi) . ') </td>';
                $html .= '<td></td>';
            }
            if ($_akumulasi === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $html .= '<tr>';
        $html .= '<td><strong>Akumulasi Penyusutan</strong></td>';

        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">(' . number_format($total_akumulasi[$in]) . ')</td>';
        }
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td><strong>Total Aktiva Tetap</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">' . number_format($total_tetap[$in] - $total_akumulasi[$in]) . '</td>';
        }
        $html .= '</tr>';

        $total_aktiva = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_aktiva, 0);

            $total_aktiva[$c] = $total_lancar[$c] + $total_tetap[$c] - $total_akumulasi[$c];
        }

        $html .= '<tr style="background-color:whitesmoke;">';
        $html .= '<td><strong>TOTAL AKTIVA</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"><strong> ' . number_format($total_aktiva[$in]) . ' </strong></td>';
        }
        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Utang Jangka Pendek</strong></td></tr>';

        $total_pendek = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_pendek, 0);
        }

        foreach ($dt['utang_pendek'] as $a) {
            $_pendek = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $pendek = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 4)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit-debet'));
                $total_pendek[$in] = $total_pendek[$in] + $pendek;
                $_pendek = $_pendek + $pendek;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $a->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($pendek) . ' </td>';
                $html .= '<td></td>';
            }
            if ($_pendek === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td><strong>Total Utang Jangka Pendek</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">' . number_format($total_pendek[$in]) . '</td>';
        }
        $html .= '</tr>';
        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Utang Jangka Panjang</strong></td></tr>';

        $total_panjang = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_panjang, 0);
        }

        foreach ($dt['utang_panjang'] as $a) {
            $_panjang = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $panjang = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 5)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit-debet'));

                $total_panjang[$in] = $total_panjang[$in] + $panjang;
                $_panjang = $_panjang + $panjang;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $a->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($panjang) . ' </td>';
                $html .= '<td></td>';
            }
            if ($_panjang === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td><strong>Total Utang Jangka Panjang</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;">' . number_format($total_panjang[$in]) . '</td>';
        }
        $html .= '</tr>';

        $html .= '<tr><td colspan="' . $kolspan1 . '"><strong>Modal</strong></td></tr>';

        $total_modal = [];
        for ($c = 0; $c <= $jumlah_periode; $c++) {
            array_push($total_modal, 0);
        }

        foreach ($dt['modal'] as $a) {
            $_modal = 0;
            $html .= '<tr>';
            for ($in = 0; $in <= $jumlah_periode; $in++) {
                $modal = DB::table('ml_journal_list')
                    ->where('asset_data_id', $a->id)
                    ->where('account_code_id', 6)
                    ->where('created', '>=', $time_array[$in]['awal'])
                    ->where('created', '<=', $time_array[$in]['akhir'])
                    ->sum(DB::raw('credit-debet'));
                $total_modal[$in] = $total_modal[$in] + $modal;
                $_modal = $_modal + $modal;

                if ($in == 0) {
                    $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; ' . $a->name . ' </td>';
                }

                $html .= '<td style="text-align:right;">' . number_format($modal) . ' </td>';
                $html .= '<td></td>';
            }
            if ($_modal === 0) {
                $html .= '<input type="hidden" class="null-data">';
            }
            $html .= '</tr>';
        }

        $html .= '<tr>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            if ($in == 0) {
                $html .= '<td>&nbsp;&nbsp;&nbsp;&nbsp; LABA/RUGI BERSIH </td>';
            }

            $html .= '<td style="text-align:right;"> ' . number_format($laba_bersih[$in]) . ' </td>';
            $html .= '<td></td>';
        }
        $html .= '<tr>';

        $html .= '<td><strong>Total Modal</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"> ' . number_format($total_modal[$in] + $laba_bersih[$in]) . ' </td>';
        }
        $html .= '</tr>';
        $html .= '<tr style="background-color:whitesmoke;">';
        $html .= '<td><strong>TOTAL UTANG DAN MODAL</strong></td>';
        for ($in = 0; $in <= $jumlah_periode; $in++) {
            $html .= '<td></td>';
            $html .= '<td style="text-align:right;"><strong> ' . number_format($total_pendek[$in] + $total_panjang[$in] + $total_modal[$in] + $laba_bersih[$in]) . '</strong></td>';
        }
        $html .= '</tr>';
        $html .= '</table>';

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function journal_report(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'journal-report';

        $awal_bulan = date('Y-m-01');
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $akhir_bulan = date('Y-m-' . $tanggal_akhir);

        // $awal_bulan = '2024-11-01';

        // $akhir_bulan = '2024-11-30';

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('main.journal_report', compact('view', 'data', 'userKey', 'from'));
    }

    public function journal_report_submit(Request $request)
    {
        $input = $request->all();
        $month_from = $input['month_from'];
        $year_from = $input['year_from'];
        $month_to = $input['month_to'];
        $year_to = $input['year_to'];

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
        $akhir_bulan = $year_to . '-' . $month_to . '-' . $tanggal_akhir;

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $html = '';
        $html .= '<table class="table">';
        $html .= '<tr>';
        $html .= '<th style="border-bottom: 2px solid black;">Tanggal</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Keterangan</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Debit</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Kredit</th>';
        $html .= '</tr>';

        $total_debet = 0;
        $total_credit = 0;

        foreach ($data as $key) {
            if ($key->transaction_name == 'Saldo Awal' && $key->is_opening_balance == 1) {
                $detail = \App\Models\JournalList::where('journal_id', $key->id)
                    ->groupBy(['asset_data_id', 'account_code_id'])
                    ->orderBy('id', 'asc')
                    ->get();

                $html .= '<tr>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '<td style="background-color:whitesmoke;"><strong>' . $key->transaction_name . '</strong></td>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '</tr>';

                foreach ($detail as $item) {
                    $total_debet = $total_debet + $this->get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['debet'];
                    $total_credit = $total_credit + $this->get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['credit'];
                    $html .= '<tr>';
                    $html .= '<td>' . date('d-m-Y', $item->created) . '</td>';
                    $html .= '<td>' . $item->asset_data_name . '</td>';
                    $html .= '<td>' . number_format($this->get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['debet']) . '</td>';
                    $html .= '<td>' . number_format($this->get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['credit']) . '</td>';
                    $html .= '</tr>';
                }
            } else {
                $detail = \App\Models\JournalList::where('journal_id', $key->id)
                    ->orderBy('id', 'asc')
                    ->get();

                $html .= '<tr>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '<td style="background-color:whitesmoke;"><strong>' . $key->transaction_name . '</strong></td>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '<td style="background-color:whitesmoke;"></td>';
                $html .= '</tr>';

                foreach ($detail as $item) {
                    $total_debet = $total_debet + $item->debet;
                    $total_credit = $total_credit + $item->credit;
                    $html .= '<tr>';
                    $html .= '<td>' . date('d-m-Y', $item->created) . '</td>';
                    $html .= '<td>' . $item->asset_data_name . '</td>';
                    $html .= '<td>' . number_format($item->debet) . '</td>';
                    $html .= '<td>' . number_format($item->credit) . '</td>';
                    $html .= '</tr>';
                }
            }
        }

        $html .= '<tr>';
        $html .= '<th style="border-top:2px solid black;">Total</th>';
        $html .= '<th style="border-top:2px solid black;"></th>';
        $html .= '<th style="border-top:2px solid black;">' . number_format($total_debet) . '</th>';
        $html .= '<th style="border-top:2px solid black;">' . number_format($total_credit) . '</th>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $html .= '</table>';

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function trial_balance(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'trial-balance';
        $awal_bulan = date('Y-m-01');
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $akhir_bulan = date('Y-m-' . $tanggal_akhir);

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->get();

        $dt['current_asset'] = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['income'] = DB::table('ml_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['capital'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['capital'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        return view('main.trial_balance', compact('view', 'data', 'dt', 'userKey', 'from'));
    }

    public function trial_balance_submit(Request $request)
    {
        $input = $request->all();
        $month_from = $input['month_from'];
        $year_from = $input['year_from'];
        $month_to = $input['month_to'];
        $year_to = $input['year_to'];

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
        $akhir_bulan = $year_to . '-' . $month_to . '-' . $tanggal_akhir;

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->get();

        $dt['current_asset'] = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['income'] = DB::table('ml_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['capital'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $html = '';
        $html .= '<table class="table" id="table-trial-balance">';
        $html .= '<tr>';
        $html .= '<th style="border-bottom: 2px solid black;">Keterangan</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Debit</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Kredit</th>';
        $html .= '</tr>';

        $total_debet = 0;
        $total_credit = 0;

        foreach ($dt['current_asset'] as $key) {
            $ca = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 1)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($ca > 0) {
                $debit = abs($ca);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($ca);
            }

            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['income'] as $key) {
            $inc = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 7)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));

            if ($inc > 0) {
                $debit = 0;
                $kredit = abs($inc);
            } else {
                $debit = abs($inc);
                $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['nb_income'] as $key) {
            $inc = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 11)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));

            if ($inc > 0) {
                $debit = 0;
                $kredit = abs($inc);
            } else {
                $debit = abs($inc);
                $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['fixed_asset'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 2)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($fa > 0) {
                $debit = abs($fa);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($fa);
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['akumulasi'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 3)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));

            if ($fa > 0) {
                $debit = 0;
                $kredit = abs($fa);
            } else {
                $debit = abs($fa);
                $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['cost_good'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 8)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($fa > 0) {
                $debit = abs($fa);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($fa);
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['admin_cost'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 10)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($fa > 0) {
                $debit = abs($fa);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($fa);
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['selling_cost'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 9)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($fa > 0) {
                $debit = abs($fa);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($fa);
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['nb_cost'] as $key) {
            $fa = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 12)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));

            if ($fa > 0) {
                $debit = abs($fa);
                $kredit = 0;
            } else {
                $debit = 0;
                $kredit = abs($fa);
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['short_debt'] as $key) {
            $sd = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 4)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));

            if ($sd > 0) {
                $debit = 0;
                $kredit = abs($sd);
            } else {
                $debit = abs($sd);
                $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['long_debt'] as $key) {
            $ld = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 5)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));

            if ($ld > 0) {
                $debit = 0;
                $kredit = abs($ld);
            } else {
                $debit = abs($ld);
                $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }

        foreach ($dt['capital'] as $key) {
            $nd = DB::table('ml_journal_list')
                ->where('asset_data_id', $key->id)
                ->where('account_code_id', 6)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir);

            if ($key->code == 'prive') {
                $ld = $nd->sum(DB::raw('debet - credit'));
                if ($ld > 0) {
                    $debit = abs($ld);
                    $kredit = 0;
                } else {
                    $debit = 0;
                    $kredit = abs($ld);
                }
            } else {
                $ld = $nd->sum(DB::raw('credit - debet'));
                if ($ld > 0) {
                    $debit = 0;
                    $kredit = abs($ld);
                } else {
                    $debit = abs($ld);
                    $kredit = 0;
                }
            }

            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $html .= '<tr>';
                $html .= '<td>' . $key->name . '</td>';
                $html .= '<td>' . number_format($debit) . '</td>';
                $html .= '<td>' . number_format(abs($kredit)) . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '<tr>';

        $html .= '<td style="border-top:2px solid black;"></td>';
        $html .= '<td style="border-top:2px solid black;"><strong>' . number_format($total_debet) . '</strong></td>';
        $html .= '<td style="border-top:2px solid black;"><strong>' . number_format(abs($total_credit)) . '</strong></td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $html .= '</table>';

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function general_ledger(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'general-ledger';
        $akun = $this->get_account_select();
        $user_id = session('id');

        return view('main.general_ledger', compact('view', 'akun', 'userKey', 'from', 'user_id'));
    }

    public function get_account_select()
    {
        $data = [];
        $group = [];

        $user_id = $this->user_id_manage(session('id'));
        $query = DB::table('ml_current_assets')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Lancar';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Lancar');

        $query = DB::table('ml_fixed_assets')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Tetap';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Tetap');

        $query = DB::table('ml_accumulated_depreciation')->where('userid', $user_id)->get();

        $dana = 12;

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Akumulasi Penyusutan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Akumulasi Penyusutan');

        $query = DB::table('ml_shortterm_debt')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Pendek';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Pendek');

        $query = DB::table('ml_longterm_debt')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Panjang';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Panjang');

        $query = DB::table('ml_capital')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Modal';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Modal');

        $query = DB::table('ml_income')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan');

        $query = DB::table('ml_cost_good_sold')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Harga Pokok Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Harga Pokok Penjualan');

        $query = DB::table('ml_selling_cost')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Penjualan');

        $query = DB::table('ml_admin_general_fees')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Umum Admin';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Umum Admin');

        $query = DB::table('ml_non_business_income')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan Di Luar Usaha';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan Di Luar Usaha');

        $query = DB::table('ml_non_business_expenses')->where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Diluar Usaha';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Diluar Usaha');

        $data['data'] = $data;
        $data['group'] = $group;

        return $data;
    }

    public function general_ledger_submit(Request $request)
    {
        $user_id = session('id');
        $input = $request->all();

        $month_from = $input['month_from'];
        $year_from = $input['year_from'];
        $month_to = $input['month_to'];
        $year_to = $input['year_to'];

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
        $akhir_bulan = $year_to . '-' . $month_to . '-' . $tanggal_akhir;

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $rules = [
            'estimation' => 'required',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o) . '<br>';
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $estimations = explode('_', $input['estimation']);
        $account_id = $estimations[0];
        $account_asset_id = $estimations[1];

        $html = '';
        $html .= '<table class="table" id="table-general-ledger">';
        $html .= '<tr>';
        $html .= '<th style="border-bottom: 2px solid black;">Tanggal</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Keterangan</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Debit</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Kredit</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Saldo</th>';
        $html .= '</tr>';

        $saldo = 0;

        $total_debit = 0;
        $total_kredit = 0;

        // $data = DB::table('ml_journal as j')->join('ml_journal_list as jl', 'jl.journal_id', '=', 'j.id', 'left')->select('jl.*','j.id as jid' ,'j.transaction_name', 'j.is_opening_balance as iob')->where('j.userid', $this->user_id_manage(session('id')))->where('jl.asset_data_id', $account_id)->where('jl.created', '>=', $awal)->where('jl.created', '<=', $akhir)->orderBy('jl.created', 'asc')->get();

        $data = Journal::where('userid', $this->user_id_manage($user_id))->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->orderBy('is_opening_balance', 'desc')->orderBy('id', 'asc')->get();

        foreach ($data as $item) {
            if ($item->transaction_name == 'Saldo Awal' && $item->is_opening_balance == 1) {
                $jlists = JournalList::where('journal_id', $item->id)
                    ->where('asset_data_id', $account_id)
                    ->where('account_code_id', $account_asset_id)
                    ->groupBy('asset_data_id')
                    ->get();

                foreach ($jlists as $jlist) {
                    if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id) {
                        $saldo = $saldo + $this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet'] - $this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit'];
                        $total_debit = $total_debit + $this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet'];
                        $total_kredit = $total_kredit + $this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit'];

                        $html .= '<tr>';
                        $html .= '<td>' . date('d-m-Y', $jlist->created) . '</td>';
                        $html .= '<td>' . $item->transaction_name . '</td>';
                        $html .= '<td>' . number_format($this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet']) . '</td>';
                        $html .= '<td>' . number_format($this->get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit']) . '</td>';
                        if ($jlist->account_code_id == 1 || $jlist->account_code_id == 2 || $jlist->account_code_id == 8 || $jlist->account_code_id == 9 || $jlist->account_code_id == 10 || $jlist->account_code_id == 12) {
                            $html .= '<td>' . number_format($saldo) . '</td>';
                        } else {
                            $html .= '<td>' . number_format(abs($saldo)) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                }
            } else {
                $jlists = JournalList::where('journal_id', $item->id)
                    ->where('asset_data_id', $account_id)
                    ->where('account_code_id', $account_asset_id)
                    ->orderBy('journal_id', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($jlists as $jlist) {
                    if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id) {
                        $saldo = $saldo + $jlist->debet - $jlist->credit;
                        $total_debit = $total_debit + $jlist->debet;
                        $total_kredit = $total_kredit + $jlist->credit;

                        $html .= '<tr>';
                        $html .= '<td>' . date('d-m-Y', $jlist->created) . '</td>';
                        $html .= '<td>' . $item->transaction_name . '</td>';
                        $html .= '<td>' . number_format($jlist->debet) . '</td>';
                        $html .= '<td>' . number_format($jlist->credit) . '</td>';
                        if ($jlist->account_code_id == 1 || $jlist->account_code_id == 2 || $jlist->account_code_id == 8 || $jlist->account_code_id == 9 || $jlist->account_code_id == 10 || $jlist->account_code_id == 12) {
                            $html .= '<td>' . number_format($saldo) . '</td>';
                        } else {
                            $html .= '<td>' . number_format(abs($saldo)) . '</td>';
                        }
                        $html .= '</tr>';
                    }
                }
            }
        }

        $html .= '<tr>';

        $html .= '<td style="border-top:2px solid black;"></td>';
        $html .= '<td style="border-top:2px solid black;"><strong>Total</strong></td>';

        $html .= '<td style="border-top:2px solid black;"><strong>' . number_format($total_debit) . '</strong></td>';
        $html .= '<td style="border-top:2px solid black;"><strong>' . number_format($total_kredit) . '</strong></td>';
        if ($account_asset_id == 1 || $account_asset_id == 2 || $account_asset_id == 8 || $account_asset_id == 9 || $account_asset_id == 10 || $account_asset_id == 12) {
            $html .= '<td style="border-top:2px solid black;"><strong>' . number_format($total_debit - $total_kredit) . '</strong></td>';
        } else {
            $html .= '<td style="border-top:2px solid black;"><strong>' . number_format(abs($total_debit - $total_kredit)) . '</strong></td>';
        }
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '<th></th>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</table>';
        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function journal_report_export($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return Excel::download(new JournalReportExport($data, $awal, $akhir), 'journal_report.xlsx');
    }

    public function journal_report_pdf($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->get();

        $pdf = Pdf::loadView('export.journal_report', compact('data', 'awal', 'akhir'))->setPaper('a4', 'potrait');

        return $pdf->stream('journal_report.pdf');
    }

    public function general_ledger_export($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $estimations = $date[2] . '_' . $date[3];

        return Excel::download(new GeneralLedgerExport($estimations, $awal, $akhir), 'general_ledger.xlsx');
    }

    public function general_ledger_pdf($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[0], $date[1]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[1] . '-' . $date[0] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);
        $estimasi = $date[2] . '_' . $date[3];

        // return Excel::download(new GeneralLedgerExport($estimations, $awal, $akhir), 'general_ledger.xlsx');
        $pdf = Pdf::loadView('export.general_ledger', compact('estimasi', 'awal', 'akhir'))->setPaper('a4', 'potrait');

        return $pdf->stream('general ledger.pdf');
    }

    public function profit_loss_export($tanggal)
    {


        $date = explode('_', $tanggal);

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


        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);



            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }



        $data = $this->list_account();
        return Excel::download(new ProfitLossExport($data, $time_array, $jumlah_periode, $start), 'profit_loss.xlsx');
    }



    public function profit_loss_pdf($tanggal)
    {
        // $date = explode('_', $tanggal);

        // $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        // $start = $date[1] . '-' . $date[0] . '-01';
        // $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;
        // $awal = strtotime($start);
        // $akhir = strtotime($end);

        // $data = $this->list_account();

        // $pdf = Pdf::loadView('export.profit_loss', compact('data', 'awal', 'akhir'))->setPaper('a4', 'potrait');

        // return $pdf->stream('profit loss.pdf');

        $date = explode('_', $tanggal);

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


        for ($b = 0; $b <= $jumlah_periode; $b++) {
            $bulan_pertama = $bulan_awal;
            $tanggal_ujung = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($bulan_awal)), date('Y', strtotime($bulan_awal)));
            $bulan_kedua = date('Y-m', strtotime($bulan_awal)) . '-' . $tanggal_ujung;

            $row['awal'] = strtotime($bulan_pertama);
            $row['akhir'] = strtotime($bulan_kedua);



            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }


        $data = $this->list_account();
        $periode = $jumlah_periode;
        $pdf = Pdf::loadView('export.profit_loss', compact('data', 'time_array', 'periode', 'start'))->setPaper('a4', 'landscape');

        return $pdf->stream('profit loss.pdf');
    }

    public function balance_sheet_export($tanggal)
    {
        $date = explode('_', $tanggal);

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

            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)));

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

        $dt = $this->list_balance_account();

        return Excel::download(new BalanceExport($dt, $start, $time_array, $jumlah_periode, $laba_bersih), 'balance_sheet.xlsx');
    }

    public function balance_sheet_pdf($tanggal)
    {
        // $date = explode('_', $tanggal);

        // $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        // $start = $date[1] . '-' . $date[0] . '-01';
        // $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;
        // $awal = strtotime($start);
        // $akhir = strtotime($end);

        // $dt = $this->list_balance_account();
        // $laba_bersih = $this->count_net_profit($date[0], $date[1], $date[2], $date[3]);
        // $pdf = Pdf::loadView('export.balance_sheet', compact('dt', 'awal', 'akhir', 'laba_bersih'))->setPaper('a4', 'potrait');

        // return $pdf->stream('laporan neraca.pdf');

        $date = explode('_', $tanggal);

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

            $laba_bersih[$b] = $this->count_net_profit(date('m', strtotime($bulan_pertama)), date('Y', strtotime($bulan_pertama)), date('m', strtotime($bulan_kedua)), date('Y', strtotime($bulan_kedua)));

            $bulan_awal = date('Y-m-d', strtotime($bulan_awal . ' + 1 month'));

            array_push($time_array, $row);
        }

        $dt = $this->list_balance_account();
        $periode = $jumlah_periode;
        $pdf = Pdf::loadView('export.balance_sheet', compact('dt', 'start', 'time_array', 'periode', 'laba_bersih'))->setPaper('a4', 'landscape');

        return $pdf->stream('laporan neraca.pdf');
    }

    public function trial_balance_export($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->get();

        $dt['current_asset'] = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['income'] = DB::table('ml_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['capital'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();
        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        return Excel::download(new TrialExport($dt, $awal, $akhir, $data), 'trial_balance.xlsx');
    }

    public function trial_balance_pdf($tanggal)
    {
        $date = explode('_', $tanggal);

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $date[2], $date[3]);

        $start = $date[1] . '-' . $date[0] . '-01';
        $end = $date[3] . '-' . $date[2] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('created', '>=', $awal)
            ->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->get();

        $dt['current_asset'] = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['income'] = DB::table('ml_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['capital'] = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();
        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();

        // return Excel::download(new TrialExport($dt, $awal, $akhir, $data), 'trial_balance.xlsx');
        $pdf = Pdf::loadView('export.trial_balance', compact('dt', 'awal', 'akhir', 'data'))->setPaper('a4', 'potrait');

        return $pdf->stream('journal_report.pdf');
    }

    public function opening_balance($bulan, $tahun)
    {
        $c_date = $tahun . '-' . $bulan . '-01';
        $custom_date = date('F', strtotime($c_date)) . '-' . $tahun;
        $this_month = $tahun . '-' . $bulan . '-01';
        $tanggal = date('Y-m-d', strtotime($this_month));
        $u_tanggal = strtotime($tanggal);

        $capital = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'modal-pemilik')
            ->first();
        // Log::debug(session('id'));
        // Log::debug(json_encode($capital));

        $c_code = $capital->id . '_' . $capital->account_code_id;

        $get_first_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' first day of previous month'));
        $get_last_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' last day of previous month'));

        $u_from = strtotime($get_first_day_of_prev_month);
        $u_to = strtotime($get_last_day_of_prev_month);

        $prive = DB::table('ml_capital')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'prive')
            ->first();

        $total_prive = JournalList::where('asset_data_id', $prive->id)
            ->where('account_code_id', 6)
            ->whereBetween('created', [$u_from, $u_to])
            ->sum(DB::raw('debet-credit'));

        $mf = date('m', strtotime($get_first_day_of_prev_month));
        $yf = date('Y', strtotime($get_first_day_of_prev_month));

        $mt = date('m', strtotime($get_last_day_of_prev_month));
        $yt = date('Y', strtotime($get_last_day_of_prev_month));

        $laba = $this->count_net_profit($mf, $yf, $mt, $yt);
        // $this->create_temp_saldo_awal($u_from, $c_code, $capital->id, $capital->name);

        // dd($laba);

        $journal_delete = Journal::where('userid', $this->user_id_manage(session('id')))
            ->where('is_opening_balance', 1)
            ->where('transaction_name', 'Saldo Awal')
            ->where('created', $u_tanggal);

        foreach ($journal_delete->get() as $jd) {
            JournalList::where('journal_id', $jd->id)->delete();
        }

        $journal_delete->delete();

        $journals = Journal::where('userid', $this->user_id_manage(session('id')))
            ->whereBetween('created', [$u_from, $u_to])
            ->orderBy('id', 'asc')
            ->get();

        // INSERT DATA KE JURNAL

        $j = new Journal();
        $j->userid = $this->user_id_manage(session('id'));
        $j->journal_id = 0;
        $j->transaction_id = 0;
        $j->transaction_name = 'Saldo Awal';
        $j->rf_accode_id = '';
        $j->st_accode_id = '';
        $j->debt_data = '';
        $j->nominal = 0;
        $j->total_balance = 0;
        $j->is_opening_balance = 1;
        $j->color_date = '#' . $this->get_random_color();
        $j->created = $u_tanggal;
        $j->save();
        $id = $j->id;

        $jl = new JournalList();
        $jl->journal_id = $id;
        $jl->rf_accode_id = '';
        $jl->st_accode_id = $c_code;
        $jl->account_code_id = $capital->account_code_id;
        $jl->asset_data_id = $capital->id;
        $jl->asset_data_name = $capital->name;
        $jl->debet = $laba - $total_prive > 0 ? 0 : abs($laba - $total_prive);
        $jl->credit = $laba - $total_prive > 0 ? abs($laba - $total_prive) : 0;
        $jl->is_debt = 0;
        $jl->is_receivables = 0;
        $jl->created = $u_tanggal;
        $jl->relasi_trx = '';
        $jl->save();

        $on = 0;

        $total_nominal_debit = 0;
        $total_nominal_kredit = 0;

        foreach ($journals as $journal) {
            $lists = DB::table('ml_journal_list as jl')
                ->select('jl.*', 'j.transaction_name', 'j.is_opening_balance')
                ->join('ml_journal as j', 'j.id', '=', 'jl.journal_id', 'left')
                ->where('jl.journal_id', $journal->id)
                ->orderBy('jl.id', 'asc')
                ->get();

            foreach ($lists as $list) {
                if ($list->account_code_id == 1 || $list->account_code_id == 2 || $list->account_code_id == 3 || $list->account_code_id == 4 || $list->account_code_id == 5) {
                    $jl = new JournalList();
                    $jl->journal_id = $id;
                    $jl->rf_accode_id = $list->rf_accode_id;
                    $jl->st_accode_id = $list->st_accode_id;
                    $jl->account_code_id = $list->account_code_id;
                    $jl->asset_data_id = $list->asset_data_id;
                    $jl->asset_data_name = $list->asset_data_name;
                    $jl->debet = $list->debet;
                    $jl->credit = $list->credit;
                    $jl->is_debt = $list->is_debt;
                    $jl->is_receivables = $list->is_receivables;
                    $jl->created = $u_tanggal;
                    $jl->relasi_trx = $list->relasi_trx;
                    $jl->save();

                    $total_nominal_debit = $total_nominal_debit + $list->debet;
                    $total_nominal_kredit = $total_nominal_kredit + $list->credit;
                } elseif ($list->account_code_id == 6) {
                    if ($list->asset_data_id == $capital->id) {
                        $jl = new JournalList();
                        $jl->journal_id = $id;
                        $jl->rf_accode_id = $list->rf_accode_id;
                        $jl->st_accode_id = $list->st_accode_id;
                        $jl->account_code_id = $list->account_code_id;
                        $jl->asset_data_id = $list->asset_data_id;
                        $jl->asset_data_name = $list->asset_data_name;
                        $jl->debet = $list->debet;
                        $jl->credit = $list->credit;
                        $jl->is_debt = $list->is_debt;
                        $jl->is_receivables = $list->is_receivables;
                        $jl->created = $u_tanggal;
                        $jl->relasi_trx = $list->relasi_trx;
                        $jl->save();

                        $total_nominal_debit = $total_nominal_debit + $list->debet;
                        $total_nominal_kredit = $total_nominal_kredit + $list->credit;

                        $on++;
                    } elseif ($list->asset_data_id == $prive->id) {
                    } else {
                        $jl = new JournalList();
                        $jl->journal_id = $id;
                        $jl->rf_accode_id = $list->rf_accode_id;
                        $jl->st_accode_id = $list->st_accode_id;
                        $jl->account_code_id = $list->account_code_id;
                        $jl->asset_data_id = $list->asset_data_id;
                        $jl->asset_data_name = $list->asset_data_name;
                        $jl->debet = $list->debet;
                        $jl->credit = $list->credit;
                        $jl->is_debt = $list->is_debt;
                        $jl->is_receivables = $list->is_receivables;
                        $jl->created = $u_tanggal;
                        $jl->relasi_trx = $list->relasi_trx;
                        $jl->save();

                        $total_nominal_debit = $total_nominal_debit + $list->debet;
                        $total_nominal_kredit = $total_nominal_kredit + $list->credit;
                    }
                } else {
                }
            }
        }

        $data = JournalList::where('journal_id', $id)->groupBy('asset_data_id')->get();

        $total_d = 0;
        $total_c = 0;
        foreach ($data as $d) {
            $total_d = $total_d + $this->get_view_data($id, $d->asset_data_id, $d->account_code_id)['debet'];
            $total_c = $total_c + $this->get_view_data($id, $d->asset_data_id, $d->account_code_id)['credit'];
        }

        Journal::where('id', $id)->update(['nominal' => $total_d, 'total_balance' => $total_d]);
    }

    protected function create_temp_saldo_awal($u_tanggal, $c_code, $capital_id, $capital_name)
    {
        $cek = Journal::where('relasi_trx', 'temp-awal')
            ->where('created', $u_tanggal)
            ->where('userid', $this->user_id_manage(session('id')));
        if ($cek->count() > 0) {
            foreach ($cek->get() as $c) {
                JournalList::where('journal_id', $c->id)->delete();
            }

            $cek->delete();
        }

        $j = new Journal();
        $j->userid = $this->user_id_manage(session('id'));
        $j->journal_id = 0;
        $j->transaction_id = 0;
        $j->transaction_name = 'Saldo Awal';
        $j->rf_accode_id = '';
        $j->st_accode_id = '';
        $j->debt_data = '';
        $j->nominal = 0;
        $j->total_balance = 0;
        $j->is_opening_balance = 1;
        $j->color_date = '#' . $this->get_random_color();
        $j->created = $u_tanggal;
        $j->relasi_trx = 'temp_awal';
        $j->save();

        $id = $j->id;

        $jl = new JournalList();
        $jl->journal_id = $id;
        $jl->rf_accode_id = '';
        $jl->st_accode_id = $c_code;
        $jl->account_code_id = 6;
        $jl->asset_data_id = $capital_id;
        $jl->asset_data_name = $capital_name;
        $jl->debet = 0;
        $jl->credit = 0;
        $jl->is_debt = 0;
        $jl->is_receivables = 0;
        $jl->created = $u_tanggal;
        $jl->relasi_trx = 'temp-awal';
        $jl->save();
    }

    public function count_net_profits($m_from, $y_from, $m_to)
    {
        $laba_bersih = 0;

        return $laba_bersih;
    }

    public static function get_view_data_controller($id, $asset_data_id, $account_code_id)
    {
        $query = JournalList::where('journal_id', $id)->where('asset_data_id', $asset_data_id)->where('account_code_id', $account_code_id);
        if ($account_code_id == 3 || $account_code_id == 4 || $account_code_id == 5 || $account_code_id == 6 || $account_code_id == 7 || $account_code_id == 11) {
            $nilai = $query->sum(DB::raw('credit-debet'));
            if ($nilai > 0) {
                $data['debet'] = 0;
                $data['credit'] = $nilai;
            } else {
                $data['debet'] = abs($nilai);
                $data['credit'] = 0;
            }
        } elseif ($account_code_id == 1 || $account_code_id == 2 || $account_code_id == 8 || $account_code_id == 9 || $account_code_id == 10 || $account_code_id == 12) {
            $nilai = $query->sum(DB::raw('debet-credit'));
            if ($nilai > 0) {
                $data['debet'] = $nilai;
                $data['credit'] = 0;
            } else {
                $data['debet'] = 0;
                $data['credit'] = abs($nilai);
            }
        }

        return $data;
    }

    public static function user_id_staff($userid)
    {
        $user = MlAccount::findorFail($userid);
        if ($user->role_code != 'staff') {
            return $user->id;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            return $branch->account_id;
        }
    }
}
