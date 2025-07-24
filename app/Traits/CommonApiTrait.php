<?php
namespace App\Traits;

use App\Models\Journal;
use App\Models\JournalList;
use App\Models\MdCustomer;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MlTransaction;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait CommonApiTrait
{
    
    public function set_color($id) {
        $data = MlTransaction::findorFail($id);
        $color = $data->color;
        return $color; 
    }
    
    
    public function send_to_journal($transaction_id, $userId)
    {
        $penjualan = Penjualan::findorFail($transaction_id);
        if ($penjualan->sync_status !== 1) {
            $untuk = $this->get_account_code(99, $penjualan->payment_method,$userId);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $this->get_account_code(7, $penjualan->payment_method,$userId) . '_' . 7;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $penjualan->paid;
            $cost_time = date('Y-m-d', strtotime($penjualan->created_at));
            $waktu = strtotime($cost_time);
            if($penjualan->customer_id == null) {
                $customer_name = $penjualan->cust_name;
            } else {
                $customer = MdCustomer::findorFail($penjualan->customer_id);
                $customer_name = $customer->name;

            }
            
            $transaction_name = 'Penjualan ( ' . $customer_name . ' ) '.$penjualan->reference;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu, $userId);
            
        }
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userId)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $akun_hpp = $this->get_account_code(8, '', $userId);
        $akun_barang = $this->get_account_code(1, '',$userId);

        $items = PenjualanProduct::where('penjualan_id', $id)->get();
        
        $hpp = 0;
        if($items->count()> 0) {
            foreach($items as $item) {
                $master = Product::findorFail($item->product_id);
                $cogs = $master->cost * $item->quantity;
                $hpp = $hpp + $cogs;
            }   
        }

        $data_journal = [
            'userid' => $userId,
            'journal_id' => 0,
            'transaction_id' => 1,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal + $hpp,
            'total_balance' => $nominal + $hpp,
            'color_date' => $this->set_color(1),
            'created' => $waktu,
            'relasi_trx' => 'pos_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        $data_list_insert = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $st,
            'account_code_id' => $ex_st[1],
            'asset_data_id' => $ex_st[0],
            'asset_data_name' => $this->get_transaction_name($ex_st[0], $ex_st[1], ''),
            'credit' => 0,
            'debet' => $nominal,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert);

        $data_list_insert2 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $rf,
            'st_accode_id' => '',
            'account_code_id' => $ex_rf[1],
            'asset_data_id' => $ex_rf[0],
            'asset_data_name' => $this->get_transaction_name($ex_rf[0], $ex_rf[1], ''),
            'credit' => $nominal,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);

        $data_list_insert3 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => '',
            'st_accode_id' => $akun_hpp.'_'. 8,
            'account_code_id' => 8,
            'asset_data_id' => $akun_hpp,
            'asset_data_name' => $this->get_transaction_name($akun_hpp, 8, ''),
            'credit' => 0,
            'debet' => $hpp,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert3);

        $data_list_insert4 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $akun_barang.'_'. 1,
            'st_accode_id' => '',
            'account_code_id' => 1,
            'asset_data_id' => $akun_barang,
            'asset_data_name' => $this->get_transaction_name($akun_barang, 1, ''),
            'credit' => $hpp,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert4);

        $me = Penjualan::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }


    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 7) {
            $account = MlIncome::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name;
        }
        elseif ($accode_code_id == 8) {
            $account = MlCostGoodSold::findorFail($untuk);
            $transaction_name = $account->name;
        }
        return $transaction_name;
    }

    protected function get_account_code($account_id, $payment,$userId)
    {
        if ($account_id == 7) {
            $data = MlIncome::where('userid', $userId)->where('code', 'penjualan-produk');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlIncome();
                $new->userid = $userId;
                $new->transaction_id = 0;
                $new->account_code_id = 7;
                $new->code = 'penjualan-produk';
                $new->name = 'Penjualan Produk';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 1) {
            $data = MlCurrentAsset::where('userid', $userId)->where('code', 'persediaan-barang-dagang');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $userId;
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persediaan-barang-dagang';
                $new->name = 'Persediaan Barang Dagang';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 8) {
            $data = MlCostGoodSold::where('userid', $userId)->where('code', 'harga-pokok-penjualan');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCostGoodSold();
                $new->userid = $userId;
                $new->transaction_id = 0;
                $new->account_code_id = 8;
                $new->code = 'harga-pokok-penjualan';
                $new->name = 'Harga Pokok Penjualan';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 99) {
            $data = MlCurrentAsset::where('userid', $userId)->where('code', $payment)->first();
            return $data->id;
        }
    }

    public function get_data_transaction($key = '', $coloum = '')
    {
        $row = DB::table('ml_transaction')->where('id', $key)->first();
        return $row->$coloum;
    }

    public function get_user($key,$userId)
    {
        $id = $userId;
        
        $row = DB::table('ml_accounts')->where('id', $id)->first();
        return $row->$key;
    }

    public function getAllListAssetWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $output = '';

        if ($account_code_id == 1) {
            $output = $this->getListCurrentAssetWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 2) {
            $output = $this->getListFixedAssetWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 3) {
            $output = $this->getListAccumulatedDepreciationWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 4) {
            $output = $this->getListShortTermDebtWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 5) {
            $output = $this->getListLongTermDebtWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 6) {
            $output = $this->getListCapitalWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 7) {
            $output = $this->getListIncomeWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 8) {
            $output = $this->getListCostGoodSoldWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 9) {
            $output = $this->getListSellingCostWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 10) {
            $output = $this->getListAdminGeneralFeesWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 11) {
            $output = $this->getListNonBusinessIncomeWithAccDataId($userid, $account_data_id, $account_code_id);
        } elseif ($account_code_id == 12) {
            $output = $this->getListNonBusinessExpensesWithAccDataId($userid, $account_data_id, $account_code_id);
        }

        return $output;
    }

    protected function getListCurrentAssetWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_current_assets')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListFixedAssetWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_fixed_assets')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListAccumulatedDepreciationWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_accumulated_depreciation')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListShortTermDebtWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_shortterm_debt')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListLongTermDebtWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_longterm_debt')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListCapitalWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_capital')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListIncomeWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_income')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListCostGoodSoldWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_cost_good_sold')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListSellingCostWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_selling_cost')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListAdminGeneralFeesWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_admin_general_fees')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListNonBusinessIncomeWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_non_business_income')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    protected function getListNonBusinessExpensesWithAccDataId($userid, $account_data_id, $account_code_id)
    {
        $row = DB::table('ml_non_business_expenses')->where('userid', $userid)->where('id', $account_data_id)->where('account_code_id', $account_code_id)->first();

        return $row->name;
    }

    public function list_account($userId)
    {
        $data['income'] = DB::table('ml_income')->where('userid', $userId)->where('account_code_id', 7)->orderBy('id')->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')->where('userid', $userId)->where('account_code_id', 8)->orderBy('id')->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $userId)->where('account_code_id', 9)->orderBy('id')->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')->where('userid', $userId)->where('account_code_id', 10)->orderBy('id')->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')->where('userid', $userId)->where('account_code_id', 11)->orderBy('id')->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')->where('userid', $userId)->where('account_code_id', 12)->orderBy('id')->get();

        return $data;
    }

    public function list_balance_account($userId)
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')->where('userid', $userId)->where('account_code_id', 1)->orderBy('id')->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')->where('userid', $userId)->where('account_code_id', 2)->orderBy('id')->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')->where('userid', $userId)->where('account_code_id', 4)->orderBy('id')->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')->where('userid', $userId)->where('account_code_id', 5)->orderBy('id')->get();
        $data['modal'] = DB::table('ml_capital')->where('userid', $userId)->where('account_code_id', 6)->orderBy('id')->get();

        return $data;
    }

    public function get_random_color()
    {
        // mt_srand((float) microtime()*1000000);

        $c = '';
        while (strlen($c) < 6) {
            $c .= sprintf('%02X', mt_rand(0, 255));
        }

        return $c;
    }
}
