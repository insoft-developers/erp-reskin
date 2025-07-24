<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalList;
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
use App\Traits\CommonApiTrait;
use App\Traits\MobileJournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\JournalTrait;
use App\Traits\LogUserTrait;

class PengaturanController extends Controller
{
    use CommonApiTrait;
    use JournalTrait;
    use LogUserTrait;
    public function modalAwalCheck(Request $request)
    {
        $query = Journal::where('userid',$this->user_id_staff($request->userid) )
            ->where('transaction_name', 'Saldo Awal')
            ->where('is_opening_balance', null);

        if ($query->count() > 0) {
            return response()->json([
                'success' => true,
                'data' => 'exist',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => 'create',
            ]);
        }
    }

    public function pengaturanModalAwal(Request $request)
    {
        $query = Journal::where('userid',$this->user_id_staff($request->userid) )
            ->where('transaction_name', 'Saldo Awal')
            ->where('is_opening_balance', null);
        $jurnal = $query->first() ?? 0;
        if ($query->count() > 0) {
            $list = JournalList::where('journal_id', $jurnal->id)
                ->orderBy('id')
                ->get();
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
                $row['asset_data_name'] = $det->asset_data_name;
                $row['debet'] = $det->debet;
                $row['credit'] = $det->credit;
                $row['is_debt'] = $det->is_debt;
                $row['is_receivables'] = $det->is_receivables;
                $row['created'] = $det->created;
                $row['relasi_trx'] = $det->relasi_trx;
                $row['group'] = $group;
                array_push($detail, $row);
            }
            $this->insert_user_log($request->userid, "pengaturan modal awal");
            return response()->json([
                'success' => true,
                'data' => $jurnal,
                'detail' => $detail,
                'tanggal' => date('d-m-Y', $jurnal->created),
            ]);
        } else {
            $list = [];

            return response()->json([
                'success' => true,
                'data' => null,
                'detail' => [],
                'tanggal' => date('d-m-Y'),
            ]);
        }
    }

    protected function get_account_select($userid)
    {
        $data = [];
        $group = [];

        $user_id = $userid;
        $query = MlCurrentAsset::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Lancar';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Lancar');

        $query = MlFixedAsset::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Aktiva Tetap';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Aktiva Tetap');

        $query = MlAccumulatedDepreciation::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Akumulasi Penyusutan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Akumulasi Penyusutan');

        $query = MlShorttermDebt::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Pendek';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Pendek');

        $query = MlLongtermDebt::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Utang Jangka Panjang';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Utang Jangka Panjang');

        $query = MlCapital::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Modal';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Modal');

        $query = MlIncome::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan');

        $query = MlCostGoodSold::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Harga Pokok Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Harga Pokok Penjualan');

        $query = MlSellingCost::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Penjualan';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Penjualan');

        $query = MlAdminGeneralFee::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Biaya Umum Admin';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Biaya Umum Admin');

        $query = MlNonBussinessIncome::where('userid', $user_id)->get();

        foreach ($query as $key) {
            $row['id'] = $key->id;
            $row['group'] = 'Pendapatan Di Luar Usaha';
            $row['account_code_id'] = $key->account_code_id;
            $row['code'] = $key->code;
            $row['name'] = $key->name;
            array_push($data, $row);
        }
        array_push($group, 'Pendapatan Di Luar Usaha');

        $query = MlNonBusinessExpense::where('userid', $user_id)->get();

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

    public function modalAwalSave(Request $request)
    {
        $input = $request->all();

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

            $ids = $input['transaction_id'];

            $journal_id = 0;

            if (empty($ids)) {
                $data_journal = [
                    'userid' =>$this->user_id_staff($input['userid']) ,
                    'transaction_id' => $get_id_transaction,
                    'transaction_name' => 'Saldo Awal',
                    'rf_accode_id' => $input['akun'][0],
                    'st_accode_id' => $input['akun'][1],
                    'nominal' => $nominal,
                    'color_date' => $this->set_color(6),
                    'created' => $date,
                ];

                $journal_id = Journal::insertGetId($data_journal);
            } else {
                $data_journal = [
                    'userid' =>$this->user_id_staff($input['userid']) ,
                    'transaction_id' => $get_id_transaction,
                    'transaction_name' => 'Saldo Awal',
                    'rf_accode_id' => $input['akun'][0],
                    'st_accode_id' => $input['akun'][1],
                    'nominal' => $nominal,
                    'color_date' => '#' . $this->get_random_color(),
                    'created' => $date,
                ];
                Journal::where('id', $ids)->update($data_journal);
                JournalList::where('journal_id', $ids)->delete();
                $journal_id = $ids;

                DB::table('ml_initial_capital')
                    ->where('userid',$this->user_id_staff($input['userid']) )
                    ->delete();
            }

            for ($i = 0; $i < count($input['akun']); $i++) {
                if (!empty($input['debit'][$i])) {
                    $debit = $input['debit'][$i] == null ? 0 : $input['debit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_staff($input['userid']), $account_code_id[0], $account_code_id[1]);
                    }

                    $data_debit = [
                        'journal_id' => $journal_id,
                        'rf_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'debet' => $debit,
                        'created' => $date,
                    ];
                    JournalList::insert($data_debit);
                }

                if (!empty($input['kredit'][$i])) {
                    $credit = $input['kredit'][$i] == null ? 0 : $input['kredit'][$i];

                    if ($input['akun'][$i] !== '') {
                        $account_code_id = explode('_', $input['akun'][$i]);
                        $asset_data_name = $this->getAllListAssetWithAccDataId($this->user_id_staff($input['userid']), $account_code_id[0], $account_code_id[1]);
                    }

                    $data_credit = [
                        'journal_id' => $journal_id,
                        'st_accode_id' => $input['akun'][$i],
                        'account_code_id' => $account_code_id[1],
                        'asset_data_id' => $account_code_id[0],
                        'asset_data_name' => $asset_data_name,
                        'credit' => $credit,
                        'created' => $date,
                    ];
                    JournalList::insert($data_credit);
                }
            }

            // Update total saldo
            $reCalculateTotalSaldo = $this->checkTotalBalance($journal_id);

            DB::table('ml_journal')
                ->where('id', $journal_id)
                ->update(['total_balance' => $reCalculateTotalSaldo]);

            if (is_array($input['akun'])) {
                for ($i = 0; $i < count($input['akun']); $i++) {
                    if (!empty($input['debit'][$i])) {
                        $debit = str_replace(',', '', $input['debit'][$i]);

                        if ($input['akun'][$i] !== '') {
                            $account_code_id = explode('_', $input['akun'][$i]);
                        }

                        $data_debit = [
                            'transaction_name' => 'Saldo Awal',
                            'userid' =>$this->user_id_staff($input['userid']) ,
                            'rf_accode_id' => '',
                            'st_accode_id' => $input['akun'][$i],
                            'account_code_id' => $account_code_id[1],
                            // 'asset_data_id'		=> $account_code_id[0],
                            // 'asset_data_name'	=> getAllListAssetWithAccDataId$this->user_id_staff($input['userid'])(, $account_code_id[0], $account_code_id[1]),
                            'debet' => $debit,
                            'credit' => 0,
                            'created' => $date,
                        ];

                        // First insert data to ml_initial_capital
                        DB::table('ml_initial_capital')->insert($data_debit);
                    }

                    if (!empty($input['kredit'][$i])) {
                        $credit = str_replace(',', '', $input['kredit'][$i]);

                        if ($input['akun'][$i] !== '') {
                            $account_code_id = explode('_', $input['akun'][$i]);
                        }

                        $data_credit = [
                            'transaction_name' => 'Saldo Awal',
                            'userid' =>$this->user_id_staff($input['userid']) ,
                            'rf_accode_id' => $input['akun'][$i],
                            'st_accode_id' => '',
                            'account_code_id' => $account_code_id[1],
                            // 'asset_data_id'		=> $account_code_id[0],
                            // 'asset_data_name'	=> getAllListAssetWithAccDataId$this->user_id_staff($input['userid'])(, $account_code_id[0], $account_code_id[1]),
                            'debet' => 0,
                            'credit' => $credit,
                            'created' => $date,
                        ];

                        // First insert data to ml_initial_capital
                        DB::table('ml_initial_capital')->insert($data_credit);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'setting modal awal berhasil',
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

    public function pengaturanRekeningDetail(Request $request)
    {
        $acc = $request->account;
        $account = $acc + 1;
        $table = '';
        $title = '';
        if ($account == 1) {
            $table = 'ml_current_assets';
            $title = 'Aktiva Lancar';
        } elseif ($account == 2) {
            $table = 'ml_fixed_assets';
            $title = 'Aktiva Tetap';
        } elseif ($account == 3) {
            $table = 'ml_accumulated_depreciation';
            $title = 'Akumulasi Penyusutan';
        } elseif ($account == 4) {
            $table = 'ml_shortterm_debt';
            $title = 'Utang Jangka Pendek';
        } elseif ($account == 5) {
            $table = 'ml_longterm_debt';
            $title = 'Utang Jangka Panjang';
        } elseif ($account == 6) {
            $table = 'ml_capital';
            $title = 'Modal';
        } elseif ($account == 7) {
            $table = 'ml_income';
            $title = 'Pendapatan';
        } elseif ($account == 9) {
            $table = 'ml_selling_cost';
            $title = 'Biaya Penjualan';
        } elseif ($account == 8) {
            $table = 'ml_cost_good_sold';
            $title = 'Harga Pokok Penjualan';
        } elseif ($account == 10) {
            $table = 'ml_admin_general_fees';
            $title = 'Biaya Umum Admin';
        } elseif ($account == 11) {
            $table = 'ml_non_business_income';
            $title = 'Pendapatan diluar Usaha';
        } elseif ($account == 12) {
            $table = 'ml_non_business_expenses';
            $title = 'Biaya diluar Usaha';
        }

        $data = DB::table($table)
            ->where('userid',$this->user_id_staff($request->userid) )
            ->orderBy('id')
            ->get();
        
        $this->insert_user_log($request->userid, "pengaturan kode rekening");
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function pengaturanRekeningSave(Request $request)
    {
        $input = $request->all();

        $account = $input['index_id'] + 1;

        if ($account == 1) {
            $table = 'ml_current_assets';
        } elseif ($account == 2) {
            $table = 'ml_fixed_assets';
        } elseif ($account == 3) {
            $table = 'ml_accumulated_depreciation';
        } elseif ($account == 4) {
            $table = 'ml_shortterm_debt';
        } elseif ($account == 5) {
            $table = 'ml_longterm_debt';
        } elseif ($account == 6) {
            $table = 'ml_capital';
        } elseif ($account == 7) {
            $table = 'ml_income';
        } elseif ($account == 9) {
            $table = 'ml_selling_cost';
        } elseif ($account == 8) {
            $table = 'ml_cost_good_sold';
        } elseif ($account == 10) {
            $table = 'ml_admin_general_fees';
        } elseif ($account == 11) {
            $table = 'ml_non_business_income';
        } elseif ($account == 12) {
            $table = 'ml_non_business_expenses';
        }

        $rules = [
            'account_item.*' => 'required',
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
            foreach ($input['id'] as $key => $value) {
                $cek = DB::table($table)->where('id', $value);
                $slug_str = str_replace(' ', '-', $input['account_item'][$key]);
                $slug = strtolower($slug_str);

                if ($cek->count() > 0) {
                    DB::table($table)
                        ->where('id', $value)
                        ->where('can_be_deleted', 3)
                        ->update([
                            'name' => $input['account_item'][$key],
                            'code' => $slug,
                        ]);
                } else {
                    DB::table($table)->insert([
                        'userid' =>$this->user_id_staff($input['userid']) ,
                        'transaction_id' => 0,
                        'account_code_id' => $input['account_code_id'],
                        'code' => $slug,
                        'name' => $input['account_item'][$key],
                        'can_be_deleted' => 3,
                        'created' => time(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function kodeRekeningDelete(Request $request) {
        $input = $request->all();
        $account = $input['table_code'] + 1;
        $id = $input['id'];

        if ($account == 1) {
            $table = 'ml_current_assets';
        } elseif ($account == 2) {
            $table = 'ml_fixed_assets';
        } elseif ($account == 3) {
            $table = 'ml_accumulated_depreciation';
        } elseif ($account == 4) {
            $table = 'ml_shortterm_debt';
        } elseif ($account == 5) {
            $table = 'ml_longterm_debt';
        } elseif ($account == 6) {
            $table = 'ml_capital';
        } elseif ($account == 7) {
            $table = 'ml_income';
        } elseif ($account == 9) {
            $table = 'ml_selling_cost';
        } elseif ($account == 8) {
            $table = 'ml_cost_good_sold';
        } elseif ($account == 10) {
            $table = 'ml_admin_general_fees';
        } elseif ($account == 11) {
            $table = 'ml_non_business_income';
        } elseif ($account == 12) {
            $table = 'ml_non_business_expenses';
        }


        $cek = JournalList::where('account_code_id', $account)
            ->where('asset_data_id', $id);

        if($cek->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Gagal Hapus, akun rekening ini sudah digunakan dalam transaksi!"
            ]);
        }

        try {
            
            DB::table($table)->where('id', $id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function generateOpeningBalance(Request $request)
    {
        try {
            $input = $request->all();
            $userid =$this->user_id_staff($input['userid']) ;
            $custom_date = $input['year'] . '-' . $input['month'];
            $this_month = $input['year'] . '-' . $input['month'] . '-01';
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

            $laba = $this->count_net_profit($u_from, $u_to, $userid);

            $journal_delete = Journal::where('userid', $userid)->where('is_opening_balance', 1)->where('created', $u_tanggal);

            foreach ($journal_delete->get() as $jd) {
                JournalList::where('journal_id', $jd->id)->delete();
            }

            $journal_delete->delete();

            $journals = Journal::where('userid', $userid)
                ->whereBetween('created', [$u_from, $u_to])
                ->get();

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
                    ->select('jl.*', 'j.transaction_name')
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
                            // dd($list->is_opening_balance);
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
                        } else if ($list->asset_data_id == $capital->id) {

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
                    }
                }
            }

            Journal::where('id', $id)->update(['nominal' => $total_nominal_debit, 'total_balance' => $total_nominal_debit]);
            $this->insert_user_log($request->userid, "generate opening balance");
            return response()->json([
                'success' => true,
                'message' => 'Generate Opening Balance Success',
                'ufrom' => $u_from,
                'uto' => $u_to,
                'first_prev' =>  $get_first_day_of_prev_month,
                'last_prev' => $get_last_day_of_prev_month,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function count_net_profit($from, $to, $userid)
    {
        $awal = $from;
        $akhir = $to;

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

    public function initialDelete(Request $request)
    {
        $input = $request->all();

        $awal = $input['tahun'] . '-' . $input['bulan'] . '-01';
        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, $input['bulan'], $input['tahun']);
        $end = $input['tahun'] . '-' . $input['bulan'] . '-' . $tanggal_akhir;

        $awal_time = strtotime($awal);
        $akhir_time = strtotime($end);

        $jurnal = Journal::where('userid',$this->user_id_staff($input['userid']) )->where('transaction_name', 'Saldo Awal')->where('is_opening_balance', 1)->where('created', '>=', $awal_time)->where('created', '<=', $akhir_time);

        foreach ($jurnal->get() as $j) {
            JournalList::where('journal_id', $j->id)->delete();
        }

        $jurnal->delete();
        $this->insert_user_log($request->userid, "hapus modal awal");
        return response()->json([
            'success' => true,
            'message' => 'Saldo Awal Deleted !',
        ]);
    }
}
