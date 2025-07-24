<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Converse;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\InterPurchase;
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
use App\Models\StockOpname;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class JournalController extends Controller
{
    use CommonApiTrait;
    use JournalTrait;
    use LogUserTrait;
    public function getAccountReceive(Request $request)
    {
        $input = $request->all();

        $data = [];
        $group = [];

        $simpan = [];
        $kelompok = [];

        $user_id = $this->user_id_staff($this->user_id_staff($input['userid']));
        $id = $input['id'];

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
            'success' => true,
            'data' => $data,
            'group' => $group,
            'simpan' => $simpan,
            'kelompok' => $kelompok,
        ]);
    }

    public function transactionType()
    {
        $data = \App\Models\MlTransaction::orderBy('id', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function list(Request $request)
    {
        $input = $request->all();

        $awal = $input['tahun'] . '-' . $input['bulan'] . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['bulan'], $input['tahun']);
        $akhir = $input['tahun'] . '-' . $input['bulan'] . '-' . $tanggal_akhir;

        $str_awal = strtotime($awal);
        $str_akhir = strtotime($akhir);

        $query = Journal::where('userid', $this->user_id_staff($this->user_id_staff($input['userid'])))
            ->where('created', '>=', $str_awal)
            ->where('created', '<=', $str_akhir);
        if (!empty($input['cari'])) {
            $query->where('transaction_name', 'like', '%' . $input['cari'] . '%');
        }

        $data = $query->orderBy('created', 'desc')->orderBy('id','desc')->get();

        $rows = [];
        foreach ($data as $key => $d) {
            $total_debit = JournalList::where('journal_id', $d->id)->sum('debet');
            $total_credit = JournalList::where('journal_id', $d->id)->sum('credit');

            $row['id'] = $d->id;
            $row['userid'] = $d->userid;
            $row['journal_id'] = $d->journal_id;
            $row['transaction_id'] = $d->transaction_id;
            $row['transaction_name'] = $d->transaction_name;
            $row['rf_accode_id'] = $d->rf_accode_id;
            $row['st_accode_id'] = $d->st_accode_id;
            $row['debt_data'] = $d->debt_data;
            $row['nominal'] = $d->nominal;
            $row['angka'] = number_format($d->nominal);
            $row['total_balance'] = $d->total_balance;
            $row['is_opening_balance'] = $d->is_opening_balance;
            $row['color_date'] = $d->color_date;
            $row['edit_count'] = $d->edit_count;
            $row['created'] = date('d-m-Y', $d->created);
            $row['tanggal'] = date('d', $d->created);
            $row['relasi_trx'] = $d->relasi_trx;
            if ($total_debit == $total_credit) {
                $row['not_balance'] = 0;
            } else {
                $row['not_balance'] = 1;
            }
            if (str_contains($d->transaction_name, 'Saldo Awal')) {
                $row['awal'] = 1;
            } else {
                $row['awal'] = 0;
            }
            array_push($rows, $row);
        }

        $this->insert_user_log($request->userid, 'journal page');

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function saveQuickJournal(Request $request)
    {
        $input = $request->all();

        $rules = [
            'tanggal_transaksi' => 'required',
            'jenis_transaksi' => 'required',
            'receive_from' => 'required',
            'save_to' => 'required',
            'keterangan' => 'required',
            'nominal' => 'required',
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

        $nominal = $input['nominal'];
        $date = strtotime($input['tanggal_transaksi']);

        $data_journal = [
            'userid' => $this->user_id_staff($this->user_id_staff($input['userid'])),
            'transaction_id' => $input['jenis_transaksi'],
            'transaction_name' => $input['keterangan'],
            'rf_accode_id' => $input['receive_from'],
            'st_accode_id' => $input['save_to'],
            'debt_data' => '',
            'nominal' => $nominal,
            'color_date' => $this->get_data_transaction($input['jenis_transaksi'], 'color'),
            'created' => $date,
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
            'asset_data_name' => $this->getAllListAssetWithAccDataId($this->get_user('id', $this->user_id_staff($input['userid'])), $account_code_id2[0], $account_code_id2[1]),
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
            'asset_data_name' => $this->getAllListAssetWithAccDataId($this->get_user('id', $this->user_id_staff($input['userid'])), $account_code_id1[0], $account_code_id1[1]),
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

        $this->insert_user_log($request->userid, 'jurnal quick akuntansi');
        return response()->json([
            'success' => true,
            'message' => 'success',
            'id' => $journal_id,
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

    public function getAccountSelect(Request $request)
    {
        $user_id = $this->user_id_staff($request->userid);

        $data = [];
        $group = [];
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
            'group' => $group,
        ]);
    }

    public function saveMultipleJournal(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_staff($input['userid']);

        $rules = [
            'akun.*' => 'required',
            'debit.*' => 'required_without:kredit.*',
            'kredit.*' => 'required_without:debit.*',
            'transaction_date' => 'required',
            'transaction_name' => 'required',
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
        try {
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

            $data_journal = [
                'userid' => $userid,
                'transaction_id' => $get_id_transaction,
                'transaction_name' => $this->checkTransactionName($input['transaction_name'], $userid),
                'rf_accode_id' => $input['akun'][0],
                'st_accode_id' => $input['akun'][1],
                'nominal' => $nominal,
                'color_date' => '#8a8980',
                'created' => $date,
                'description' => $input['description'],
            ];

            // First insert data to journal
            // $this->db->sql_insert($data_journal, 'ml_journal');
            $journal_id = DB::table('ml_journal')->insertGetId($data_journal);

            for ($i = 0; $i < count($input['akun']); $i++) {
                if (!empty($input['debit'][$i])) {
                    $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        // $asset_data_name = $this->getAllListAssetWithAccDataId($userid, $account_code_id[0], $account_code_id[1]);
                        $asset_data_name = $this->setAssetDataName($account_code_id[1], $account_code_id[0]);
                    }

                    $data_debit = [
                        'journal_id' => $journal_id,
                        'rf_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'debet' => $debit,
                        'created' => $date,
                        'description' => $input['catatan'][$i],
                    ];
                    DB::table('ml_journal_list')->insert($data_debit);
                }

                if (!empty($input['kredit'][$i])) {
                    $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        // $asset_data_name = $this->getAllListAssetWithAccDataId($userid, $account_code_id[0], $account_code_id[1]);
                        $asset_data_name = $this->setAssetDataName($account_code_id[1], $account_code_id[0]);
                    }

                    $data_credit = [
                        'journal_id' => $journal_id,
                        'st_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'credit' => $credit,
                        'created' => $date,
                        'description' => $input['catatan'][$i],
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
            $this->insert_user_log($request->userid, 'save journal');
            return response()->json([
                'success' => true,
                'message' => 'success',
                'id' => $journal_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
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

    public function checkTransactionName($transaction_name, $userid)
    {
        $row = DB::table('ml_journal')->where('userid', $userid)->where('transaction_name', $transaction_name)->get();

        if ($row->count() > 0) {
            $total = $row->count() + 1;

            $output = $transaction_name . ' (' . $total . ')';
        } else {
            $output = $transaction_name;
        }

        return $output;
    }

    public function journalDelete(Request $request)
    {
        $input = $request->all();
        $check = DB::table('ml_journal')
            ->where('id', $input['id'])
            ->where('userid', $this->user_id_staff($input['userid']))
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
                } elseif ($judul == 'konversi') {
                    $me = Converse::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'opname') {
                    $me = StockOpname::findorFail($komponen_id);
                    $me->sync_status = 0;
                    $me->save();
                } elseif ($judul == 'manufacturing') {
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
                ->where('userid', $this->user_id_staff($input['userid']))
                ->delete();

            $row_delete_journallist = DB::table('ml_journal_list')->where('journal_id', $input['id'])->get();

            foreach ($row_delete_journallist as $rd) {
                DB::table('ml_journal_list')->where('journal_id', $rd->journal_id)->delete();
            }
            $this->insert_user_log($request->userid, 'delete journal');
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

    public function JournalEdit(Request $request)
    {
        $id = $request->id;
        $jurnal = DB::table('ml_journal')->where('id', $id)->first();
        $list = DB::table('ml_journal_list')->where('journal_id', $id)->orderBy('id')->get();
        $detail = [];
        foreach ($list as $det) {
            if ($det->account_code_id == 1) {
                $group = 'Aktiva Lancar';
            } elseif ($det->account_code_id == 2) {
                $group = 'Aktiva Tetap';
            } elseif ($det->account_code_id == 3) {
                $group = 'Akumulasi Penyusutan';
            } elseif ($det->account_code_id == 4) {
                $group = 'Utang Jangka Pendek';
            } elseif ($det->account_code_id == 5) {
                $group = 'Utang Jangka Panjang';
            } elseif ($det->account_code_id == 6) {
                $group = 'Modal';
            } elseif ($det->account_code_id == 7) {
                $group = 'Pendapatan';
            } elseif ($det->account_code_id == 8) {
                $group = 'Harga Pokok Penjualan';
            } elseif ($det->account_code_id == 9) {
                $group = 'Biaya Penjualan';
            } elseif ($det->account_code_id == 10) {
                $group = 'Biaya Umum Admin';
            } elseif ($det->account_code_id == 11) {
                $group = 'Pendapatan Diluar Usaha';
            } elseif ($det->account_code_id == 12) {
                $group = 'Biaya Diluar Usaha';
            }

            $row['id'] = $det->id;
            $row['journal_id'] = $det->journal_id;
            $row['rf_accode_id'] = $det->rf_accode_id;
            $row['st_accode_id'] = $det->st_accode_id;
            $row['account_code_id'] = $det->account_code_id;
            $row['asset_data_id'] = $det->asset_data_id;
            $row['asset_data_name'] = $this->setAssetDataName($det->account_code_id, $det->asset_data_id);
            $row['debet'] = $det->debet;
            $row['credit'] = $det->credit;
            $row['is_debt'] = $det->is_debt;
            $row['is_receivables'] = $det->is_receivables;
            $row['created'] = $det->created;
            $row['relasi_trx'] = $det->relasi_trx;
            $row['group'] = $group;
            $row['description'] = $det->description;
            array_push($detail, $row);
        }

        $tanggal = date('d-m-Y', $jurnal->created);

        $data['jurnal'] = $jurnal;
        $data['list'] = $detail;
        $data['tanggal'] = $tanggal;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    protected function setAssetDataName($account_code_id, $asset_data_id)
    {
        if ($account_code_id == 1) {
            $detail = MlCurrentAsset::findorFail($asset_data_id);
        } elseif ($account_code_id == 2) {
            $detail = MlFixedAsset::findorFail($asset_data_id);
        } elseif ($account_code_id == 3) {
            $detail = MlAccumulatedDepreciation::findorFail($asset_data_id);
        } elseif ($account_code_id == 4) {
            $detail = MlShorttermDebt::findorFail($asset_data_id);
        } elseif ($account_code_id == 5) {
            $detail = MlLongtermDebt::findorFail($asset_data_id);
        } elseif ($account_code_id == 6) {
            $detail = MlCapital::findorFail($asset_data_id);
        } elseif ($account_code_id == 7) {
            $detail = MlIncome::findorFail($asset_data_id);
        } elseif ($account_code_id == 8) {
            $detail = MlCostGoodSold::findorFail($asset_data_id);
        } elseif ($account_code_id == 9) {
            $detail = MlSellingCost::findorFail($asset_data_id);
        } elseif ($account_code_id == 10) {
            $detail = MlAdminGeneralFee::findorFail($asset_data_id);
        } elseif ($account_code_id == 11) {
            $detail = MlNonBussinessIncome::findorFail($asset_data_id);
        } elseif ($account_code_id == 12) {
            $detail = MlNonBusinessExpense::findorFail($asset_data_id);
        }

        return $detail->name;
    }

    public function journalUpdate(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_staff($input['userid']);
        $row = DB::table('ml_journal')->where('id', $input['transaction_id'])->first();

        $rules = [
            'akun.*' => 'required',
            'debit.*' => 'required_without:kredit.*',
            'kredit.*' => 'required_without:debit.*',
            'transaction_date' => 'required',
            'transaction_name' => 'required',
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

        try {
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

            $data_journal = [
                'userid' => $userid,
                'transaction_id' => $get_id_transaction,
                'transaction_name' => $input['transaction_name'],
                'rf_accode_id' => $input['akun'][1],
                'st_accode_id' => $input['akun'][0],
                'nominal' => $nominal,
                'created' => $date,
                'description' => $input['description'],
            ];

            // First update data to journal
            DB::table('ml_journal')->where('id', $input['transaction_id'])->update($data_journal);

            $journal_id = $input['transaction_id'];

            DB::table('ml_journal_list')->where('journal_id', $journal_id)->delete();

            for ($i = 0; $i < count($input['akun']); $i++) {
                if (!empty($input['debit'][$i])) {
                    $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        $asset_data_name = $this->getAllListAssetWithAccDataId($userid, $account_code_id[0], $account_code_id[1]);
                    }

                    $data_debit = [
                        'journal_id' => $journal_id,
                        'rf_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'debet' => $debit,
                        'created' => $date,
                        'description' => $input['catatan'][$i],
                    ];

                    DB::table('ml_journal_list')->insert($data_debit);
                }

                if (!empty($input['kredit'][$i])) {
                    $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        $asset_data_name = $this->getAllListAssetWithAccDataId($userid, $account_code_id[0], $account_code_id[1]);
                    }

                    $data_credit = [
                        'journal_id' => $journal_id,
                        'st_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'credit' => $credit,
                        'created' => $date,
                        'description' => $input['catatan'][$i],
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
            $this->insert_user_log($request->userid, 'update journal');
            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function journalPreview(Request $request)
    {
        $input = $request->all();
        $jurnal = Journal::findorFail($input['journal_id']);
        $list = JournalList::where('journal_id', $input['journal_id'])
            ->groupBy(['asset_data_id', 'account_code_id'])
            ->orderBy('account_code_id')
            ->get();
        $tanggal = date('d F Y', $jurnal->created);

        $total_debit = 0;
        $total_kredit = 0;

        $rows = [];
        foreach ($list as $l) {
            $total_debit = $total_debit + $this->get_value_data($input['journal_id'], $l->asset_data_id, $l->account_code_id)['debet'];
            $total_kredit = $total_kredit + $this->get_value_data($input['journal_id'], $l->asset_data_id, $l->account_code_id)['credit'];

            $row['asset_data_name'] = $this->get_asset_data_name($l->asset_data_id, $l->account_code_id, $input['userid']);
            $row['debet'] = $this->get_value_data($input['journal_id'], $l->asset_data_id, $l->account_code_id)['debet'];
            $row['credit'] = $this->get_value_data($input['journal_id'], $l->asset_data_id, $l->account_code_id)['credit'];
            $row['catatan'] = $l->description;
            array_push($rows, $row);
        }

        $data['jurnal'] = $jurnal;
        $data['list'] = $rows;
        $data['tanggal'] = $tanggal;
        $data['total_debit'] = $total_debit;
        $data['total_kredit'] = $total_kredit;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function journalUpload(Request $request)
    {
        $ids = $request->ids;

        $path = storage_path('app/public/images/journal');

        try {
            if ($request->has('image')) {
                $manager = new ImageManager(new Driver());
                $file = $request->image;
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $img = $manager->read($file->path());
                $img->resize(500, 500, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path . '/' . $filename);
            } else {
                return response()->json(['message' => trans('/storage/test/' . 'def.png.')], 200);
            }

            $data = Journal::findorFail((int) $ids);
            $data->image = $filename;
            $data->save();

            return response()->json(['message' => trans('/storage/test/' . $filename)], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    protected function get_asset_data_name($asset_data_id, $account_code_id, $userid)
    {
        if ($account_code_id == 1) {
            $data = MlCurrentAsset::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 2) {
            $data = MlFixedAsset::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 3) {
            $data = MlAccumulatedDepreciation::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 4) {
            $data = MlShorttermDebt::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 5) {
            $data = MlLongtermDebt::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 6) {
            $data = MlCapital::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 7) {
            $data = MlIncome::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 8) {
            $data = MlCostGoodSold::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 9) {
            $data = MlSellingCost::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 10) {
            $data = MlAdminGeneralFee::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 11) {
            $data = MlNonBussinessIncome::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        } elseif ($account_code_id == 12) {
            $data = MlNonBusinessExpense::where('userid', $this->user_id_staff($userid))->where('id', $asset_data_id)->first();
        }

        return $data->name;
    }
}
