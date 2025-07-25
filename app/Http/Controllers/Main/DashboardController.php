<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Converse;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\InterPurchase;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\MlTransaction;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MaterialPurchase;
use App\Models\MdAdjustment;
use App\Models\MdExpense;
use App\Models\MlAccumulatedDepreciation;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCapital;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\MlFixedAsset;
use App\Models\MlIncome;
use App\Models\MlLongtermDebt;
use App\Models\MlNonBusinessExpense;
use App\Models\MlNonBussinessIncome;
use App\Models\MlSellingCost;
use App\Models\MlShorttermDebt;
use App\Models\MtPengeluaranOutlet;
use App\Models\Penjualan;
use App\Models\ProductManufacture;
use App\Models\ProductPurchase;
use App\Models\Receivable;
use App\Models\ReceivablePaymentHistory;
use App\Models\Shrinkage;
use App\Models\StockOpname;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    use CommonTrait;

    public function journal_add()
    {
        $view = 'journal-add';
        $akun = $this->get_account_select();
        return view('main.journal_add', compact('view', 'akun'));
    }

    public function save_multiple_journal(Request $request)
    {
        $input = $request->all();
        $rules = [
            'akun.*' => 'required',
            'debit.*' => 'required_without:kredit.*',
            'kredit.*' => 'required_without:debit.*',
            'transaction_date' => 'required',
            'transaction_name' => 'required',
        ];


        if ($request->hasFile('image')) {
            $rules['image'] = 'max:3072';
        }

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

        $nominal = '';
        if (empty($input['debit'][0])) {
            $nominal = $input['kredit'][0];
            $input['debit'][0] = 0;
        }
        if (empty($input['kredit'][0])) {
            $nominal = $input['debit'][0];
            $input['kredit'][0] = 0;
        }

        $date = strtotime($input['transaction_date']);

        $get_id_transaction = $this->initTransactionId($input['akun'][0], $input['akun'][1]);

        $input['image'] = null;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/journal', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'transaction_id' => $get_id_transaction,
            'transaction_name' => $this->checkTransactionName($input['transaction_name']),
            'rf_accode_id' => $input['akun'][0],
            'st_accode_id' => $input['akun'][1],
            'nominal' => $nominal,
            'color_date' => '#8a8980',
            'created' => $date,
            'description' => $input['description'],
            'image' => $input['image'],
        ];

        // First insert data to journal
        // $this->db->sql_insert($data_journal, 'ml_journal');
        $journal_id = DB::table('ml_journal')->insertGetId($data_journal);

        for ($i = 0; $i < count($input['akun']); $i++) {
            if (!empty($input['debit'][$i])) {
                $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_debit = [
                    'journal_id' => $journal_id,
                    'rf_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'debet' => $debit,
                    'created' => $date,
                    'description' => $input['kick_note'][$i],
                ];
                DB::table('ml_journal_list')->insert($data_debit);
            }

            if (!empty($input['kredit'][$i])) {
                $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_credit = [
                    'journal_id' => $journal_id,
                    'st_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'credit' => $credit,
                    'created' => $date,
                    'description' => $input['kick_note'][$i],
                ];
                DB::table('ml_journal_list')->insert($data_credit);
            }
        }

        // Update total saldo
        $reCalculateTotalSaldo = $this->checkTotalBalance($journal_id);

        // Second update data to journal
        // $this->db->sql_update(['total_balance' => $reCalculateTotalSaldo], 'ml_journal', ['id' => $journal_id]);
        DB::table('ml_journal')
            ->where('id', $journal_id)
            ->update(['total_balance' => $reCalculateTotalSaldo]);

        $session_data = [
            'bulan' => date('m', strtotime($request->transaction_date)),
            'tahun' => date('Y', strtotime($request->transaction_date)),
        ];
        session(['sess_periode' => $session_data]);

        return response()->json([
            'success' => true,
        ]);
    }

    protected function initTransactionId($account_code_id1, $account_code_id2)
    {
        $var_rf_cid = explode('_', $account_code_id1);
        $rf_id = $var_rf_cid[0];
        $rf_code_id = $var_rf_cid[1];

        $var_st_cid = explode('_', $account_code_id2);
        $st_id = $var_st_cid[0];
        $st_code_id = $var_st_cid[1];

        $row1 = DB::table('ml_transaction_subcat')->where('account_code_id', $rf_code_id)->where('received_from_status', 0)->orderBy('id')->get();
        foreach ($row1 as $rw1) {
            if ($rw1->transaction_id !== 3 && $rw1->transaction_id !== 5) {
                $output1[] = $rw1->transaction_id;
            }
        }

        $row2 = DB::table('ml_transaction_subcat')->where('account_code_id', $st_code_id)->where('saved_to_status', 0)->orderBy('id')->get();
        foreach ($row2 as $rw2) {
            if ($rw2->transaction_id !== 3 && $rw2->transaction_id !== 5) {
                $output2[] = $rw2->transaction_id;
            }
        }

        $output3 = array_uintersect($output1, $output2, 'strcasecmp');

        $k = array_rand($output3);
        $new_output = $output3[$k];

        return $new_output;
    }

    public function journal_table(Request $request)
    {
        
        $input = $request->all();
        
        $awal = $input['tahun'] . '-' . $input['bulan'] . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['bulan'], $input['tahun']);
        $akhir = $input['tahun'] . '-' . $input['bulan'] . '-' . $tanggal_akhir;

        $session_data = [
            'bulan' => $input['bulan'],
            'tahun' => $input['tahun'],
        ];
        session(['sess_periode' => $session_data]);

        $str_awal = strtotime($awal);
        $str_akhir = strtotime($akhir);

        // $query = DB::table('ml_journal')
        $query = Journal::where('userid', $this->user_id_manage(session('id')))
            // ->where('transaction_name', '!=', 'Saldo Awal')
            ->where('created', '>=', $str_awal)
            ->where('created', '<=', $str_akhir);
        if (!empty($input['cari'])) {
            $query->where('transaction_name', 'like', '%' . $input['cari'] . '%');
        }

        if($input['inbalance'] =='inbalance') {
            $query->whereHas('journal_list', function ($query) {
                $query->selectRaw('SUM(debet) as total_debet');
            })->withSum('journal_list as total_debet', 'debet')
            ->whereHas('journal_list', function ($query) {
                $query->selectRaw('SUM(credit) as total_credit');
            })->withSum('journal_list as total_credit', 'credit')
            ->havingRaw('total_debet != total_credit');
        }


        $data = $query->get();
        return Datatables::of($data)
            ->addColumn('transaction_name', function ($data) {
                if ($data->edit_count > 0) {
                    return $data->transaction_name . ' ( ' . $data->edit_count . ' )';
                } else {
                    return $data->transaction_name;
                }
            })
            ->addColumn('dibuat', function ($data) {
                return '<center>' . date('d-m-Y', $data->created) . '</center>';
            })
            ->addColumn('tanggal', function ($data) {
                return '<center><div class="date-box" style="background:' . $data->color_date . '">' . date('d', $data->created) . '</div></center>';
            })
            ->addColumn('total_balance', function ($data) {
               
                $debit = $data->journal_list->sum('debet');
                $kredit = $data->journal_list->sum('credit');

                if ($debit == $kredit) {
                    return '<div sytle="text-align:right";>Rp. ' . number_format($data->total_balance) . '</div>';
                } else {
                    return '<div sytle="text-align:right;"><span title="jurnal tidak balance" style="cursor:pointer;color:red;"><strong>Rp. ' . number_format($data->total_balance) . '</strong></span></div>';
                }
                
            })
            ->addColumn('action', function ($data) {
                if (str_contains($data->transaction_name, 'Saldo Awal')) {
                    return '<center><button onclick="preview_journal(' . $data->id . ')" style="width:70px;margin-bottom:5px;" class="btn btn-info btn-sm">Lihat</button><button onclick="journal_delete(' . $data->id . ')" style="width:70px;" class="btn btn-danger btn-sm">Hapus</button></center>';
                } else {
                    return '<center><button onclick="preview_journal(' . $data->id . ')" style="width:70px;margin-bottom:5px;" class="btn btn-info btn-sm">Lihat</button><a href="' . url('journal_edit/' . $data->id) . '"><button style="width:70px;margin-bottom:5px;" class="btn btn-warning btn-sm">Sunting</button></a><button onclick="journal_delete(' . $data->id . ')" style="width:70px;" class="btn btn-danger btn-sm">Hapus</button></center>';
                }
            })
            ->rawColumns(['action', 'dibuat', 'tanggal', 'total_balance', 'transaction_name'])
            ->make(true);
    }

    public function index()
    {
        $view = 'dashboard';
        return view('main.dashboard_new', compact('view'));
    }


    public function journal_list()
    {
        $view = 'journal-list';
        $list_transaksi = MlTransaction::orderBy('id', 'asc')->get();
        return view('reskin.journal.journal_list', compact('view', 'list_transaksi'));
    }

    public function save_jurnal(Request $request)
    {
        $input = $request->all();

        $rules = [
            'tanggal_transaksi' => 'required',
            'jenis_transaksi' => 'required',
            'receive_from' => 'required',
            'save_to' => 'required',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'max:3072';
        }

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

        $nominal = $input['nominal'];
        $date = strtotime($input['tanggal_transaksi']);

        $input['image'] = null;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/journal', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'transaction_id' => $input['jenis_transaksi'],
            'transaction_name' => $this->checkTransactionName($input['keterangan']),
            'rf_accode_id' => $input['receive_from'],
            'st_accode_id' => $input['save_to'],
            'debt_data' => '',
            'nominal' => $nominal,
            'color_date' => $this->get_data_transaction($input['jenis_transaksi'], 'color'),
            'created' => $date,
            'image' => $input['image'],
        ];

        // First insert data to journal
        // $this->db->sql_insert($data_journal, 'ml_journal');

        $journal_id = DB::table('ml_journal')->insertGetId($data_journal);

        // Get last ID Journal after insert

        if ($input['receive_from'] !== '') {
            $account_code_id1 = explode('_', $input['receive_from']);
        }

        if ($input['save_to'] !== '') {
            $account_code_id2 = explode('_', $input['save_to']);
        }

        $data_journal_st_accode = [
            'journal_id' => $journal_id,
            'st_accode_id' => $input['save_to'],
            'account_code_id' => $account_code_id2[1],
            'asset_data_id' => $account_code_id2[0],
            'asset_data_name' => $this->getAllListAssetWithAccDataId($this->get_user('id'), $account_code_id2[0], $account_code_id2[1]),
            'debet' => $nominal,
            'created' => $date,
        ];

        // First insert data to journal list
        // $this->db->sql_insert($data_journal_st_accode, 'ml_journal_list');
        DB::table('ml_journal_list')->insert($data_journal_st_accode);

        $data_journal_rf_accode = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $input['receive_from'],
            'account_code_id' => $account_code_id1[1],
            'asset_data_id' => $account_code_id1[0],
            'asset_data_name' => $this->getAllListAssetWithAccDataId($this->get_user('id'), $account_code_id1[0], $account_code_id1[1]),
            'credit' => $nominal,
            'created' => $date,
        ];

        // Second insert data to journal list
        // $this->db->sql_insert($data_journal_rf_accode, 'ml_journal_list');
        DB::table('ml_journal_list')->insert($data_journal_rf_accode);

        // Update total saldo
        $reCalculateTotalSaldo = $this->checkTotalBalance($journal_id);

        DB::table('ml_journal')
            ->where('id', $journal_id)
            ->update(['total_balance' => $reCalculateTotalSaldo]);

        $session_data = [
            'bulan' => date('m', strtotime($request->tanggal_transaksi)),
            'tahun' => date('Y', strtotime($request->tanggal_transaksi)),
        ];

       
       

        return response()->json([
            'success' => true,
            'message' => 'success',
            'periode' => $session_data
        ]);
    }

    public function checkTotalBalance($journal_id)
    {
        $total_all_debit = 0;
        $total_all_credit = 0;

        $i = 0;

        $bindParam_journal_list = DB::table('ml_journal_list')->where('journal_id', $journal_id)->get();

        foreach ($bindParam_journal_list as $key) {
            $total_all_debit += $key->debet;
            $total_all_credit += $key->credit;
        }

        $new_output['total_all_debit'] = $total_all_debit;
        $new_output['total_all_credit'] = $total_all_credit;

        if ($new_output['total_all_debit'] == $new_output['total_all_credit']) {
            $output = $new_output['total_all_debit'];
        } else {
            $new_total_all_dc = $new_output['total_all_debit'] - $new_output['total_all_credit'];

            $output = $new_total_all_dc;
        }

        return $output;
    }

    public function checkTransactionName($transaction_name)
    {
        $row = DB::table('ml_journal')
            ->where('userid', $this->user_id_manage(session('id')))
            ->where('transaction_name', $transaction_name)
            ->get();

        if ($row->count() > 0) {
            $total = $row->count() + 1;

            $output = $transaction_name . ' (' . $total . ')';
        } else {
            $output = $transaction_name;
        }

        return $output;
    }

    public function get_account_receive($id)
    {
        $data = [];
        $group = [];

        $simpan = [];
        $kelompok = [];

        $user_id = $this->user_id_manage(session('id'));

        if ($id == 2 || $id == 5 || $id == 8 || $id == 9 || $id == 10) {
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
        }

        if ($id == 2 || $id == 8 || $id == 9 || $id == 10) {
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
        }

        if ($id == 10) {
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
        }

        if ($id == 3 || $id == 10) {
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
        }

        if ($id == 1 || $id == 5 || $id == 10) {
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
        }

        if ($id == 5 || $id == 7) {
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
        }

        if ($id == 10) {
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
        }

        if ($id == 10) {
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
        }

        if ($id == 10) {
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
        }

        if ($id == 1 || $id == 5 || $id == 10) {
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
        }

        if ($id == 10) {
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
        }

        if ($id == 3 || $id == 10) {
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
        }
        // ================== simpan ke==================================

        if ($id == 1 || $id == 2 || $id == 3 || $id == 5 || $id == 7 || $id == 9 || $id == 10) {
            $q = DB::table('ml_current_assets')->where('userid', $user_id);
            if ($id == 5) {
                $q->where('code', 'piutang-usaha');
            }

            $query = $q->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Aktiva Lancar';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Aktiva Lancar');
        }

        if ($id == 2 || $id == 3 || $id == 7 || $id == 9 || $id == 10) {
            $query = DB::table('ml_fixed_assets')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Aktiva Tetap';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Aktiva Tetap');
        }

        if ($id == 10) {
            $query = DB::table('ml_accumulated_depreciation')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Akumulasi Penyusutan';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Akumulasi Penyusutan');
        }

        if ($id == 10) {
            $query = DB::table('ml_shortterm_debt')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Utang Jangka Pendek';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Utang Jangka Pendek');
        }

        if ($id == 10) {
            $query = DB::table('ml_income')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Pendapatan';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Pendapatan');
        }

        if ($id == 1 || $id == 2 || $id == 10) {
            $query = DB::table('ml_cost_good_sold')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Harga Pokok Penjualan';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Harga Pokok Penjualan');
        }

        if ($id == 2 || $id == 3 || $id == 10) {
            $query = DB::table('ml_selling_cost')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Biaya Penjualan';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Biaya Penjualan');
        }

        if ($id == 2 || $id == 3 || $id == 10) {
            $query = DB::table('ml_admin_general_fees')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Biaya Umum Admin';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Biaya Umum Admin');
        }

        if ($id == 10) {
            $query = DB::table('ml_non_business_income')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Pendapatan Diluar Usaha';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Pendapatan Diluar Usaha');
        }

        if ($id == 2 || $id == 3 || $id == 10) {
            $query = DB::table('ml_non_business_expenses')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Biaya Diluar Usaha';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Biaya Diluar Usaha');
        }

        if ($id == 8) {
            $query = DB::table('ml_capital')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Modal';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Modal');
        }

        if ($id == 10) {
            $query = DB::table('ml_longterm_debt')->where('userid', $user_id)->get();

            foreach ($query as $key) {
                $row['id'] = $key->id;
                $row['group'] = 'Utang Jangka Panjang';
                $row['account_code_id'] = $key->account_code_id;
                $row['code'] = $key->code;
                $row['name'] = $key->name;
                array_push($simpan, $row);
            }
            array_push($kelompok, 'Utang Jangka Panjang');
        }

        return response()->json([
            'data' => $data,
            'group' => $group,
            'simpan' => $simpan,
            'kelompok' => $kelompok,
        ]);
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

    public function journal_multiple_form()
    {
        $data = $this->get_account_select();
        return response()->json([
            'data' => $data,
            'success' => true,
        ]);
    }

    public function journal_edit($id)
    {
        $view = 'journal-edit';
        $akun = $this->get_account_select();
        $data = DB::table('ml_journal')->where('id', $id)->first();
        $detail = DB::table('ml_journal_list')->where('journal_id', $id)->orderBy('id')->get();
        return view('main.journal_edit', compact('view', 'akun', 'data', 'detail'));
    }

    public function get_detail($id)
    {
        $data = DB::table('ml_journal_list')->where('journal_id', $id)->orderBy('id')->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function confirm_journal_delete(Request $request)
    {
        $input = $request->all();
        $check = DB::table('ml_journal')
            ->where('id', $input['id'])
            ->where('userid', $this->user_id_manage(session('id')))
            ->count();

        if ($check) {
            $relasi = Journal::findorFail($input['id']);
            $trx = $relasi->relasi_trx;

            if (!empty($trx)) {
                if ($trx == 'refundx') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delete fails, refund journal can not be deleted!.',
                    ]);
                }

                $komponen = explode('_', $trx);
                $judul = $komponen[0];
                $komponen_id = $komponen[1];

                if ($judul == 'biaya') {
                    $me = MdExpense::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'utang') {
                    $me = Debt::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'payment') {
                    $me = DebtPaymentHistory::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'piutang') {
                    $me = Receivable::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'pembayaran') {
                    $me = ReceivablePaymentHistory::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'purchasing') {
                    $me = ProductPurchase::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'interpurchase') {
                    $me = InterPurchase::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'material') {
                    $me = MaterialPurchase::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'adjustment') {
                    $me = MdAdjustment::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'rekap') {
                    $me = MtPengeluaranOutlet::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'pos') {
                    $me = Penjualan::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'invoice') {
                    $me = Invoice::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } 
                elseif ($judul == 'konversi') {
                    $me = Converse::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                }
                elseif ($judul == 'opname') {
                    $me = StockOpname::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } 
                
                elseif ($judul == 'manufacturing') {
                    $me = ProductManufacture::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'penyusutan') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delete fails, please delete this journal from penyusutan menu!.',
                    ]);
                }
            }

            DB::table('ml_journal')
                ->where('id', $input['id'])
                ->where('userid', $this->user_id_manage(session('id')))
                ->delete();

            $row_delete_journallist = DB::table('ml_journal_list')
                ->where('journal_id', $input['id'])
                ->get();

            foreach ($row_delete_journallist as $rd) {
                DB::table('ml_journal_list')
                    ->where('journal_id', $rd->journal_id)
                    ->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'failed, no data to delete!',
            ]);
        }
    }

    public function journal_update(Request $request)
    {
        $input = $request->all();

        $row = DB::table('ml_journal')
            ->where('id', $input['transaction_id'])
            ->first();

        $rules = [
            'akun.*' => 'required',
            'debit.*' => 'required_without:kredit.*',
            'kredit.*' => 'required_without:debit.*',
            'transaction_date' => 'required',
            'transaction_name' => 'required',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'max:3072';
        }

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

        $nominal = '';
        if (empty($input['debit'][0])) {
            $nominal = $input['kredit'][0];
            $input['debit'][0] = 0;
        }
        if (empty($input['kredit'][0])) {
            $nominal = $input['debit'][0];
            $input['kredit'][0] = 0;
        }
        $date = strtotime($input['transaction_date']);

        $get_id_transaction = $row->transaction_id;

        $jn = Journal::findorFail($input['transaction_id']);

        $input['image'] = $jn->image;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/journal', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'transaction_id' => $get_id_transaction,
            'transaction_name' => $input['transaction_name'],
            'rf_accode_id' => $input['akun'][1],
            'st_accode_id' => $input['akun'][0],
            'nominal' => $nominal,
            'created' => $date,
            'description' => $input['description'],
            'image' => $input['image'],
        ];

        // First update data to journal
        DB::table('ml_journal')
            ->where('id', $input['transaction_id'])
            ->update($data_journal);

        $journal_id = $input['transaction_id'];

        DB::table('ml_journal_list')->where('journal_id', $journal_id)->delete();

        for ($i = 0; $i < count($input['akun']); $i++) {
            if (!empty($input['debit'][$i])) {
                $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_debit = [
                    'journal_id' => $journal_id,
                    'rf_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'debet' => $debit,
                    'created' => $date,
                    'description' => $input['kick_note'][$i],
                ];

                DB::table('ml_journal_list')->insert($data_debit);
            }

            if (!empty($input['kredit'][$i])) {
                $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                if ($input['akun'][$i] !== '') {
                    $account_code_id = explode('_', $input['akun'][$i]);
                    $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_manage(session('id')), $account_code_id[0], $account_code_id[1]);
                }

                $data_credit = [
                    'journal_id' => $journal_id,
                    'st_accode_id' => $input['akun'][$i],
                    'account_code_id' => $account_code_id[1],
                    'asset_data_id' => $account_code_id[0],
                    'asset_data_name' => $asset_data_name,
                    'credit' => $credit,
                    'created' => $date,
                    'description' => $input['kick_note'][$i],
                ];
                DB::table('ml_journal_list')->insert($data_credit);
            }
        }

        $reCalculateTotalSaldo = $this->checkTotalBalance($journal_id);

        DB::table('ml_journal')
            ->where('id', $journal_id)
            ->update([
                'edit_count' => $row->edit_count + 1,
                'total_balance' => $reCalculateTotalSaldo,
            ]);

        $sess_bulan = date('m', strtotime($request->transaction_date));
        $sess_tahun = date('Y', strtotime($request->transaction_date));

        $session_data = [
            'bulan' => $sess_bulan,
            'tahun' => $sess_tahun,
        ];
        session(['sess_periode' => $session_data]);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function ribuan($angka)
    {
        $angka_ribuan = number_format($angka);
        $angka_baru = str_replace(',', '.', $angka_ribuan);
        return $angka_baru;
    }

    public function lihat_saldo_awal($id)
    {
        $header = Journal::findorFail($id);
        $detail = JournalList::where('journal_id', $id)
            ->orderBy('id')
            ->groupBy(['asset_data_id', 'account_code_id'])
            ->get();
        // $detail = JournalList::where('journal_id', $id)->orderBy('id')->get();

        $html = '';
        $html .= '<h3>' . date('d F Y', $header->created) . ' - ' . $header->transaction_name . '</h3>';
        $html .= '<table class="table table-striped mtop20">';
        $html .= '<tr>';
        $html .= '<th width="*">Estimasi</th>';
        $html .= '<th width="20%">Debit</th>';
        $html .= '<th width="20%">Kredit</th>';
        $html .= '<th width="25%">Catatan</th>';
        $html .= '</tr>';

        $total_debit = 0;
        $total_kredit = 0;

        if ($detail->count() > 0) {
            foreach ($detail as $index => $det) {
                $total_debit = $total_debit + $this->get_view_data($id, $det->asset_data_id, $det->account_code_id)['debet'];
                $total_kredit = $total_kredit + $this->get_view_data($id, $det->asset_data_id, $det->account_code_id)['credit'];

                // $total_debit = $total_debit + $det->debet;
                // $total_kredit = $total_kredit + $det->credit;

                if ($det->debet > 0 || $det->credit > 0) {
                    // if($cek === false) {

                    $html .= '<tr>';
                    $html .= '<td>' . $this->get_asset_data_name($det->asset_data_id, $det->account_code_id) . '</td>';
                    $html .= '<td>' . number_format($this->get_view_data($id, $det->asset_data_id, $det->account_code_id)['debet']) . '</td>';
                    $html .= '<td>' . number_format($this->get_view_data($id, $det->asset_data_id, $det->account_code_id)['credit']) . '</td>';
                    $html .= '<td>' . $det->description . '</td>';
                    $html .= '</tr>';

                    // $html .= '<tr>';
                    // $html .= '<td>' . $det->asset_data_name . '</td>';
                    // $html .= '<td>' . number_format($det->debet) . '</td>';
                    // $html .= '<td>' . number_format($det->credit) . '</td>';
                    // $html .= '</tr>';
                    // }
                }
            }
        }

        $html .= '<tr>';
        $html .= '<th>Total</th>';
        $html .= '<th>' . number_format($total_debit) . '</th>';
        $html .= '<th>' . number_format($total_kredit) . '</th>';
        $html .= '<th></th>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<th>Catatan Pribadi</th>';
        $html .= '<td colspan="3">' . $header->description . '</td>';

        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<th>Foto Dokumen Transaksi</th>';

        if ($header->image != null) {
            $html .= '<td colspan="3"><a href="' . Storage::url('images/journal/' . $header->image) . '" target="_blank"><img class="journal-images-preview" src="' . Storage::url('images/journal/' . $header->image) . '"></a></td>';
        }

        $html .= '</tr>';

        $html .= '</table';

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function automate_journal($id)
    {
        $this->send_to_journal($id);
    }

    protected function get_asset_data_name($asset_data_id, $account_code_id)
    {
        
        
        if ($account_code_id == 1) {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 2) {
            $data = MlFixedAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 3) {
            $data = MlAccumulatedDepreciation::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 4) {
            $data = MlShorttermDebt::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 5) {
            $data = MlLongtermDebt::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 6) {
            $data = MlCapital::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 7) {
            $data = MlIncome::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 8) {
            $data = MlCostGoodSold::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 9) {
            $data = MlSellingCost::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 10) {
            $data = MlAdminGeneralFee::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 11) {
            $data = MlNonBussinessIncome::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        } elseif ($account_code_id == 12) {
            $data = MlNonBusinessExpense::where('userid', $this->user_id_manage(session('id')))
                ->where('id', $asset_data_id)
                ->first();
        }

        return $data->name;
    }
}
