<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalList;
use App\Traits\CommonTrait;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use CommonTrait;
    use JournalTrait;
    use LogUserTrait;
    
    public function journalReport(Request $request)
    {
        $input = $request->all();
        $month_from = $input['month_from'];
        $year_from = $input['year_from'];
        $userid = $this->user_id_staff($input['userid']) ;

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_from, $year_from);
        $akhir_bulan = $year_from . '-' . $month_from . '-' . $tanggal_akhir;

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $jurnals = Journal::where('userid', $userid)->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')
         ->orderBy('id','asc')
        ->get();
        $row_jurnal = [];

        $total_debet = 0;
        $total_credit = 0;
        foreach ($jurnals as $jurnal) {
            $row['id'] = $jurnal->id;
            $row['transaction_name'] = $jurnal->transaction_name;
            $row['transaction_date'] = date('d-m-Y', $jurnal->created);

            if($jurnal->transaction_name == 'Saldo Awal' && $jurnal->is_opening_balance == 1) {
                $jlist = JournalList::where('journal_id', $jurnal->id)->groupBy(['asset_data_id', 'account_code_id'])->orderBy('account_code_id')->get();

                $row['list'] = [];
                foreach ($jlist as $jl) {
                    $r['asset_data_name'] = $jl->asset_data_name;
                    $r['debet'] = $this->get_value_data($jurnal->id, $jl->asset_data_id, $jl->account_code_id)['debet'];
                    $r['credit'] = $this->get_value_data($jurnal->id, $jl->asset_data_id, $jl->account_code_id)['credit'];
                    array_push($row['list'], $r);

                    $total_debet = $total_debet + $this->get_value_data($jurnal->id, $jl->asset_data_id, $jl->account_code_id)['debet'];
                    $total_credit = $total_credit + $this->get_value_data($jurnal->id, $jl->asset_data_id, $jl->account_code_id)['credit'];
                }

                array_push($row_jurnal, $row);
            } else {
                $jlist = JournalList::where('journal_id', $jurnal->id)
                ->orderBy('id','asc')
                ->get();

                $row['list'] = [];
                foreach ($jlist as $jl) {
                    $r['asset_data_name'] = $jl->asset_data_name;
                    $r['debet'] = $jl->debet;
                    $r['credit'] = $jl->credit;
                    array_push($row['list'], $r);

                    $total_debet = $total_debet + $jl->debet;
                    $total_credit = $total_credit + $jl->credit;
                }

                array_push($row_jurnal, $row);
            }
            
        }
        $this->insert_user_log($request->userid, "journal report");
        return response()->json([
            'success' => true,
            'data' => $row_jurnal,
            'debet' => $total_debet,
            'credit' => $total_credit,
        ]);
    }

    public function accountByUser(Request $request)
    {
        $input = $request->all();

        $data = [];
        $group = [];

        $user_id = $this->user_id_staff($input['userid']) ;
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

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function generalLedger(Request $request)
    {
        $input = $request->all();

        $month_from = $input['month_from'];
        $year_from = $input['year_from'];

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_from, $year_from);
        $akhir_bulan = $year_from . '-' . $month_from . '-' . $tanggal_akhir;

        

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
                $html .= str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $estimations = explode('_', $input['estimation']);
        $account_id = $estimations[0];
        $account_asset_id = $estimations[1];


        // $data = DB::table('ml_journal as j')->join('ml_journal_list as jl', 'jl.journal_id', '=', 'j.id', 'left')->select('jl.*', 'j.transaction_name')->where('j.userid', $this->user_id_staff($input['userid']) )->where('jl.asset_data_id', $account_id)->where('jl.created', '>=', $awal)->where('jl.created', '<=', $akhir)->orderBy('jl.created', 'asc')->get();


        $saldo = 0;

        $total_debit = 0;
        $total_kredit = 0;


        $data = Journal::where('userid', $this->user_id_staff($input['userid']) )->where('created', '>=', $awal)->where('created', '<=', $akhir)
            ->orderBy('created', 'asc')
            ->orderBy('is_opening_balance', 'desc')
            ->orderBy('id','asc')
            ->get();
        

        $rows = [];
        foreach ($data as $item) {

            if($item->transaction_name == 'Saldo Awal' && $item->is_opening_balance == 1 ) {
                $jlists = JournalList::where('journal_id', $item->id)->groupBy(['asset_data_id', 'account_code_id'])->get();
                foreach($jlists as $jlist) {

                    if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id) {
                        $saldo = $saldo + $this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet'] - $this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit'];
                        $total_debit = $total_debit + $this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet'];
                        $total_kredit = $total_kredit + $this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit'];

                        $row['transaction_date'] = date('d-m-Y', $item->created);
                        $row['transaction_name'] = $item->transaction_name;
                        $row['debet'] = number_format($this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet']);
                        $row['credit'] = number_format($this->get_value_data($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit']);
                        if ($item->account_code_id == 1 || $item->account_code_id == 2 || $item->account_code_id == 8 || $item->account_code_id == 9 || $item->account_code_id == 10 || $item->account_code_id == 12) {
                            $row['saldo'] = number_format($saldo);
                        } else {
                            $row['saldo'] = number_format(abs($saldo));
                        }
                        array_push($rows, $row);
                    }
                    
                }
            } else {
                $jlists = JournalList::where('journal_id', $item->id)
                ->orderBy('journal_id','asc')
                ->orderBy('id','asc')
                ->get();
                foreach($jlists as $jlist) {

                    if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id) {
                        $saldo = $saldo + $jlist->debet - $jlist->credit;
                        $total_debit = $total_debit + $jlist->debet;
                        $total_kredit = $total_kredit + $jlist->credit;

                        $row['transaction_date'] = date('d-m-Y', $item->created);
                        $row['transaction_name'] = $item->transaction_name;
                        $row['debet'] = number_format($jlist->debet);
                        $row['credit'] = number_format($jlist->credit);
                        if ($item->account_code_id == 1 || $item->account_code_id == 2 || $item->account_code_id == 8 || $item->account_code_id == 9 || $item->account_code_id == 10 || $item->account_code_id == 12) {
                            $row['saldo'] = number_format($saldo);
                        } else {
                            $row['saldo'] = number_format(abs($saldo));
                        }
                        array_push($rows, $row);
                    }
                    
                }
                
               
            }

            
        }

        if ($account_asset_id == 1 || $account_asset_id == 2 || $account_asset_id == 8 || $account_asset_id == 9 || $account_asset_id == 10 || $account_asset_id == 12) {
            $total_saldo = number_format($total_debit - $total_kredit);
        } else {
            $total_saldo =  number_format(abs($total_debit - $total_kredit));
        }

        $this->insert_user_log($request->userid, "general ledger");        
        return response()->json([
            'success' => true,
            'data' => $rows,
            'total_debet' => number_format($total_debit),
            'total_credit' =>  number_format($total_kredit),
            'total_saldo' => $total_saldo
        ]);
    }

    public function trialBalance(Request $request)
    {
        $input = $request->all();
        $month_from = $input['month_from'];
        $year_from = $input['year_from'];

        $awal_bulan = $year_from . '-' . $month_from . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $month_from, $year_from);
        $akhir_bulan = $year_from . '-' . $month_from . '-' . $tanggal_akhir;

        $awal = strtotime($awal_bulan);
        $akhir = strtotime($akhir_bulan);

        $data = DB::table('ml_journal')->where('userid', $this->user_id_staff($input['userid']) )->where('created', '>=', $awal)->where('created', '<=', $akhir)->orderBy('created', 'asc')->get();

        $dt['current_asset'] = DB::table('ml_current_assets')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['fixed_asset'] = DB::table('ml_fixed_assets')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['short_debt'] = DB::table('ml_shortterm_debt')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['long_debt'] = DB::table('ml_longterm_debt')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['income'] = DB::table('ml_income')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['cost_good'] = DB::table('ml_cost_good_sold')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['capital'] = DB::table('ml_capital')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['nb_income'] = DB::table('ml_non_business_income')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['admin_cost'] = DB::table('ml_admin_general_fees')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $dt['nb_cost'] = DB::table('ml_non_business_expenses')->where('userid', $this->user_id_staff($input['userid']) )->get();
        $dt['akumulasi'] = DB::table('ml_accumulated_depreciation')->where('userid', $this->user_id_staff($input['userid']) )->get();

        $html = '';
        $html .= '<table class="table" id="table-trial-balance">';
        $html .= '<tr>';
        $html .= '<th style="border-bottom: 2px solid black;">Keterangan</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Debit</th>';
        $html .= '<th style="border-bottom: 2px solid black;">Kredit</th>';
        $html .= '</tr>';

        $total_debet = 0;
        $total_credit = 0;

        $rows = [];

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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                 $kredit = $fa;
            } else {
                 $debit = abs($fa);
                 $kredit = 0;
            }
            $total_debet = $total_debet + $debit;
            $total_credit = $total_credit + $kredit;

            if ($debit + $kredit != 0) {
                $row['name'] = $key->name;
                $row['debet'] = number_format(abs($debit));
                $row['credit'] = number_format($kredit);
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
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
                $row['name'] = $key->name;
                $row['debet'] = number_format($debit);
                $row['credit'] = number_format(abs($kredit));
                array_push($rows, $row);
            }
        }

        $this->insert_user_log($request->userid, "trial balance (neraca saldo)");
        
        return response()->json([
            'success' => true,
            'data' => $rows,
            'total_debet' => number_format($total_debet),
            'total_credit' => number_format(abs($total_credit))
        ]);
    }

    public function profitLoss(Request $request)
    {
        $input = $request->all();

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['month_from'], $input['year_from']);

        $start = $input['year_from'] . '-' . $input['month_from'] . '-01';
        $end = $input['year_from'] . '-' . $input['month_from'] . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = $this->list_account_api($this->user_id_staff($input['userid']) );
        
        $rows = [];

        $total_income = 0;
        $row['header'] = "Pendapatan";
       

        $subs = [];
        foreach ($data['income'] as $i) {
            $income = DB::table('ml_journal_list')
                ->where('asset_data_id', $i->id)
                ->where('account_code_id', 7)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit - debet'));
            $total_income = $total_income + $income;

            if ($income != 0) {
                $sub['name'] = $i->name;
                $sub['amount'] = number_format($income);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Pendapatan Bersih';
        $row['footer_value'] = number_format($total_income);
        $row['final'] = '';
        $row['final_value'] = '';
        array_push($rows, $row);


        

        $row['header'] = 'Harga Pokok Penjualan';
       
        $total_hpp = 0;
        $subs = [];
        foreach ($data['hpp'] as $a) {
            $hpp = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 8)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_hpp = $total_hpp + $hpp;
            if ($hpp != 0) {
             
                $sub['name'] = $a->name;
                $sub['amount'] = '('.number_format($hpp).')';
                array_push($subs, $sub);
            }
        }
        $row['content'] = $subs;
        $row['footer'] = 'Total Harga Pokok Penjualan';
        $row['footer_value'] = '('.number_format($total_hpp).')';
        $laba_rugi_kotor = $total_income - $total_hpp;
        $row['final'] = 'LABA/RUGI KOTOR';
        $row['final_value'] = $laba_rugi_kotor > 0 ? number_format($laba_rugi_kotor) : '('.number_format($laba_rugi_kotor).')';
    
       
        array_push($rows, $row);


        $row['header'] = "Biaya Penjualan";

        $total_selling_cost = 0;

        $subs = [];
        foreach ($data['selling_cost'] as $a) {
            $selling_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 9)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_selling_cost = $total_selling_cost + $selling_cost;
            if ($selling_cost != 0) {
              
                $sub['name'] = $a->name;
                $sub['amount'] = '('.number_format($selling_cost).')';
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = "Total Biaya Penjualan";
        $row['footer_value'] = '('.number_format($total_selling_cost).')';
        $row['final'] = '';
        $row['final_value'] = '';
        array_push($rows, $row);


        $row['header'] = 'Biaya Umum Admin';
       
        $total_general_fees = 0;
        $subs=[];
        foreach ($data['general_fees'] as $a) {
            $general_fees = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 10)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_general_fees = $total_general_fees + $general_fees;
            if ($general_fees != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = '('.number_format($general_fees).')';
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Biaya Admin dan Umum';
        $row['footer_value'] = '('.number_format($total_general_fees).')';
        $row['final'] = '';
        $row['final_value'] = '';
        array_push($rows, $row);
       
        $row['header'] = 'Pendapatan Diluar Usaha';
        $total_nb_income = 0;


        $subs = [];
        foreach ($data['non_business_income'] as $a) {
            $nb_income = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 11)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_nb_income = $total_nb_income + $nb_income;

            if ($nb_income != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = number_format($nb_income);
                array_push($subs, $sub);
            }
        }
        $row['content'] = $subs;
        $row['footer'] = 'Total Pendapatan Diluar Usaha';
        $row['footer_value'] = number_format($total_nb_income);
        $row['final'] = '';
        $row['final_value'] = '';
        array_push($rows, $row);


        $row['header'] = 'Biaya Diluar Usaha';
        $total_nb_cost = 0;
        $subs = [];
        foreach ($data['non_business_cost'] as $a) {
            $nb_cost = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 12)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_nb_cost = $total_nb_cost + $nb_cost;

            if ($nb_cost != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = '('.number_format($nb_cost).')';
                array_push($subs, $sub);

            }
        }
        $row['content'] = $subs;
        $row['footer'] = 'Total Biaya Diluar Usaha';
        $row['footer_value'] = '('.number_format($total_nb_cost).')';
        $row['final'] = 'LABA/RUGI BERSIH';
        $laba_bersih = $laba_rugi_kotor - $total_selling_cost - $total_general_fees + $total_nb_income - $total_nb_cost;
        $row['final_value'] = $laba_bersih > 0 ? number_format($laba_bersih) : '('.number_format(abs($laba_bersih)).')';
        array_push($rows, $row);

        

        $this->insert_user_log($request->userid, "profit loss report");
        return response()->json([
            'success' => true,
            'data' => $rows,
            'kotor' => number_format($laba_rugi_kotor),
            'bersih' => number_format($laba_bersih)
        ]);

       
    }

    protected function list_account_api($userId)
    {
        $data['income'] = DB::table('ml_income')
            ->where('userid', $userId)
            ->where('account_code_id', 7)
            ->orderBy('id')
            ->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')
            ->where('userid', $userId)
            ->where('account_code_id', 8)
            ->orderBy('id')
            ->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')
            ->where('userid', $userId)
            ->where('account_code_id', 9)
            ->orderBy('id')
            ->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')
            ->where('userid', $userId)
            ->where('account_code_id', 10)
            ->orderBy('id')
            ->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')
            ->where('userid', $userId)
            ->where('account_code_id', 11)
            ->orderBy('id')
            ->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')
            ->where('userid', $userId)
            ->where('account_code_id', 12)
            ->orderBy('id')
            ->get();

        return $data;
    }

    public function balanceSheet(Request $request)
    {
        $input = $request->all();

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['month_from'], $input['year_from']);

        $start = $input['year_from'] . '-' . $input['month_from'] . '-01';
        $end = $input['year_from'] . '-' . $input['month_from'] . '-' . $tanggal_akhir;
        

        $awal = strtotime($start);
        $akhir = strtotime($end);

        $custom_date = $input['year_from'] . '-' . $input['month_from'];
        // $this_month = $input['year'] . '-' . $input['month'] . '-01';
        // $tanggal = date('Y-m-d', strtotime($this_month));
        // $u_tanggal = strtotime($tanggal);

        $get_first_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' first day of previous month'));
        $get_last_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' last day of previous month'));

        $fm = date('m', strtotime($get_first_day_of_prev_month));
        $fy = date('Y', strtotime($get_first_day_of_prev_month));

        $tm = date('m', strtotime($get_last_day_of_prev_month));
        $ty = date('Y', strtotime($get_last_day_of_prev_month));

        // $u_from = strtotime($get_first_day_of_prev_month);
        // $u_to = strtotime($get_last_day_of_prev_month);

        
        $this->opening_balance($input['month_from'], $input['year_from'], $this->user_id_staff($input['userid']) );

        $dt = $this->list_balance_account($this->user_id_staff($input['userid']) );
        $laba_bersih = $this->count_net_profit($input['month_from'], $input['year_from'], $this->user_id_staff($input['userid']) );
        
        $rows = [];
        $row['header'] = 'Aktiva Lancar';
        $total_lancar = 0;

        $subs = [];
        foreach ($dt['aktiva_lancar'] as $i) {
            $lancar = DB::table('ml_journal_list')
                ->where('asset_data_id', $i->id)
                ->where('account_code_id', 1)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet - credit'));
            $total_lancar = $total_lancar + $lancar;

            if ($lancar != 0) {
                $sub['name'] = $i->name;
                $sub['amount'] = number_format($lancar);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Aktiva Lancar';
        $row['footer_value'] = number_format($total_lancar);
        $row['final'] = '';
        $row['final_value'] ='';

        array_push($rows, $row);

        $row['header'] = 'Aktiva Tetap';

        $total_tetap = 0;
        $subs = [];
        foreach ($dt['aktiva_tetap'] as $a) {
            $tetap = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 2)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('debet-credit'));
            $total_tetap = $total_tetap + $tetap;

            if ($tetap != 0) {
             

                $sub['name'] = $a->name;
                $sub['amount'] = number_format($tetap);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Aktiva Tetap';
        $row['footer_value'] = number_format($total_tetap);

        array_push($rows, $row);

        $row['header'] = 'Akumulasi Penyusutan';

        $total_akumulasi = 0;
        $subs = [];
        foreach ($dt['akumulasi'] as $a) {
            $akumulasi = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 3)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_akumulasi = $total_akumulasi + $akumulasi;

            if ($akumulasi != 0) {
             

                $sub['name'] = $a->name;
                $sub['amount'] = number_format($akumulasi);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Akumulasi';
        $row['footer_value'] = number_format($total_akumulasi * -1 );




        $total_aktiva = $total_lancar + $total_tetap - $total_akumulasi;

        $row['final'] = 'TOTAL AKTIVA';
        $row['final_value'] =number_format($total_aktiva);
        array_push($rows, $row);

        $row['header'] = 'Utang Jangka Pendek';

        $total_pendek = 0;
        $subs = [];
        foreach ($dt['utang_pendek'] as $a) {
            $pendek = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 4)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_pendek = $total_pendek + $pendek;

            if ($pendek != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = number_format($pendek);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Utang Jangka Pendek';
        $row['footer_value'] = number_format($total_pendek);
        $row['final'] = '';
        $row['final_value'] ='';
        array_push($rows, $row);

        $row['header'] = 'Utang Jangka Panjang';

        $subs = [];
        $total_panjang = 0;
        foreach ($dt['utang_panjang'] as $a) {
            $panjang = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 5)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_panjang = $total_panjang + $panjang;

            if ($panjang != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = number_format($panjang);
                array_push($subs, $sub);
            }
        }

        $row['content'] = $subs;
        $row['footer'] = 'Total Utang Jangka Panjang';
        $row['footer_value'] = number_format($total_panjang);
        $row['final'] = '';
        $row['final_value'] ='';
        array_push($rows, $row);

        $row['header'] = 'Modal';

        $total_modal = 0;

        $subs = [];
        foreach ($dt['modal'] as $a) {
            $modal = DB::table('ml_journal_list')
                ->where('asset_data_id', $a->id)
                ->where('account_code_id', 6)
                ->where('created', '>=', $awal)
                ->where('created', '<=', $akhir)
                ->sum(DB::raw('credit-debet'));
            $total_modal = $total_modal + $modal;

            if ($modal != 0) {
                $sub['name'] = $a->name;
                $sub['amount'] = number_format($modal);
                array_push($subs, $sub);
            }
        }

    

        $sub['name'] = 'LABA/RUGI BERSIH';
        $sub['amount'] = number_format($laba_bersih);
        array_push($subs, $sub);


        $row['content'] = $subs;
        $row['footer'] = 'Total Modal';
        $row['footer_value'] = number_format($total_modal + $laba_bersih);
        $row['final'] = 'TOTAL UTANG DAN MODAL';
        $row['final_value'] =number_format($total_pendek + $total_panjang + $total_modal + $laba_bersih);

        array_push($rows, $row);

        $this->insert_user_log($request->userid, "balance sheet (neraca)");
        return response()->json([
            'success' => true,
            'data' => $rows
        ]);
    }

    public function opening_balance($bulan, $tahun, $userid)
    {
        $c_date = $tahun . '-' . $bulan . '-01';
        $custom_date = date('F', strtotime($c_date)) . '-' . $tahun;
        $this_month = $tahun . '-' . $bulan . '-01';
        $tanggal = date('Y-m-d', strtotime($this_month));
        $u_tanggal = strtotime($tanggal);

        $capital = DB::table('ml_capital')->where('userid', $userid)->where('code', 'modal-pemilik')->first();

        $c_code = $capital->id . '_' . $capital->account_code_id;

        $get_first_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' first day of previous month'));
        $get_last_day_of_prev_month = date('Y-m-d', strtotime($custom_date . ' last day of previous month'));

        $u_from = strtotime($get_first_day_of_prev_month);
        $u_to = strtotime($get_last_day_of_prev_month);

        $prive = DB::table('ml_capital')->where('userid', $userid)->where('code', 'prive')->first();

        $total_prive = JournalList::where('asset_data_id', $prive->id)
            ->where('account_code_id', 6)
            ->whereBetween('created', [$u_from, $u_to])
            ->sum(DB::raw('debet-credit'));

        $mf = date('m', strtotime($get_first_day_of_prev_month));
        $yf = date('Y', strtotime($get_first_day_of_prev_month));

        $mt = date('m', strtotime($get_last_day_of_prev_month));
        $yt = date('Y', strtotime($get_last_day_of_prev_month));

        $laba = $this->count_net_profit($mf, $yf, $userid);
        // $this->create_temp_saldo_awal($u_from, $c_code, $capital->id, $capital->name);

        // dd($laba);

        $journal_delete = Journal::where('userid', $userid)->where('is_opening_balance', 1)->where('transaction_name', 'Saldo Awal')->where('created', $u_tanggal);

        foreach ($journal_delete->get() as $jd) {
            JournalList::where('journal_id', $jd->id)->delete();
        }

        $journal_delete->delete();

        $journals = Journal::where('userid', $userid)
            ->whereBetween('created', [$u_from, $u_to])
            ->orderBy('id', 'asc')
            ->get();

        // INSERT DATA KE JURNAL

        $j = new Journal();
        $j->userid = $userid;
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
                    }else if ($list->asset_data_id == $prive->id) {

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
        Journal::where('id', $id)->update(['nominal' => $total_nominal_debit, 'total_balance' => $total_nominal_debit]);
    }


    public function list_balance_account($userid)
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')->where('userid', $userid)->where('account_code_id', 1)->orderBy('id')->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')->where('userid', $userid)->where('account_code_id', 2)->orderBy('id')->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')->where('userid', $userid)->where('account_code_id', 4)->orderBy('id')->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')->where('userid', $userid)->where('account_code_id', 5)->orderBy('id')->get();
        $data['modal'] = DB::table('ml_capital')->where('userid', $userid)->where('account_code_id', 6)->orderBy('id')->get();
        $data['akumulasi'] = DB::table('ml_accumulated_depreciation')->where('userid', $userid)->where('account_code_id', 3)->orderBy('id')->get();

        return $data;
    }


    public function count_net_profit($m_from, $y_from, $userid)
    {
        $start = $y_from . '-' . $m_from . '-01';

        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $m_from, $y_from);
        $end = $y_from . '-' . $m_from . '-' . $tanggal_akhir;
        $awal = strtotime($start);
        $akhir = strtotime($end);

        $data = $this->list_account_api($userid);
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
