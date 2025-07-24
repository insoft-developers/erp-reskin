<?php
namespace App\Traits;

use App\Models\Branch;
use App\Models\InterProduct;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\Material;
use App\Models\MdCustomer;
use App\Models\MlAccount;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MlTransaction;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use App\Models\ProductComposition;
use Illuminate\Support\Facades\DB;

trait CommonTrait
{
    

    public function user_id_manage($userid) {
        $user = MlAccount::findorFail($userid);
        if($user->role_code != 'staff') {
            return $user->id;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            return $branch->account_id;
        }
    }


    public function username_manage($userid) {
        $user = MlAccount::findorFail($userid);
        if($user->role_code != 'staff') {
            return $user->username;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            $account = MlAccount::findorFail($branch->account_id);
            return $account->username;
        }
    }

    public function get_view_data($id, $asset_data_id, $account_code_id)
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


    public function set_color($id) {
        $data = MlTransaction::findorFail($id);
        $color = $data->color;
        return $color; 
    }
    
    
    public function send_to_journal($transaction_id)
    {
        $penjualan = Penjualan::findorFail($transaction_id);
        if ($penjualan->sync_status !== 1) {
            $untuk = $this->get_account_code(99, $penjualan->payment_method);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $this->get_account_code(7, $penjualan->payment_method) . '_' . 7;
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
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu);
            
        }
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $akun_hpp = $this->get_account_code(8, '');
        $akun_barang = $this->get_account_code(1, '');

        $items = PenjualanProduct::where('penjualan_id', $id)->get();
        
        $hpp = 0;
        $cost_produk = 0;
        $cost_bahan_baku = 0;
        $cost_setengah_jadi = 0;
        if($items->count()> 0) {
            foreach($items as $item) {
                $master = Product::findorFail($item->product_id);
                $cogs = $master->cost * $item->quantity;
                
                if($master->created_by == 1) {
                    $kom = ProductComposition::where('product_id', $item->product_id)->get();
                    foreach($kom as $komposisi) {
                        if($komposisi->product_type == 1) {
                            $materi = Material::findorFail($komposisi->material_id);
                            $item_cost = $materi->cost * $komposisi->quantity * $item->quantity;
                            $cost_bahan_baku = $cost_bahan_baku + $item_cost;
                        } else if($komposisi->product_type == 2) {
                            $materi = InterProduct::findorFail($komposisi->material_id);
                            $item_cost = $materi->cost * $komposisi->quantity * $item->quantity;
                            $cost_setengah_jadi = $cost_setengah_jadi + $item_cost;
                        }

                        $hpp = $hpp + $item_cost; 
                    }
                } else {
                    $hpp = $hpp + $cogs;
                    $cost_produk = $cost_produk + $cogs;
                }
            }   
        }

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
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


        if($cost_produk > 0) {
            $data_list_insert4 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_barang.'_'. 1,
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $akun_barang,
                'asset_data_name' => $this->get_transaction_name($akun_barang, 1, ''),
                'credit' => $cost_produk,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert4);
        }
        

        if($cost_bahan_baku > 0) {
            $data_list_insert5 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $this->get_account_code(188, '').'_'. 1,
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_account_code(188, ''),
                'asset_data_name' => $this->get_transaction_name($this->get_account_code(188, ''), 1, ''),
                'credit' => $cost_bahan_baku,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert5);
        }

        if($cost_setengah_jadi > 0) {
            $data_list_insert6 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $this->get_account_code(189, '').'_'. 1,
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_account_code(189, ''),
                'asset_data_name' => $this->get_transaction_name($this->get_account_code(189, ''), 1, ''),
                'credit' => $cost_setengah_jadi,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert6);
        }

        

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

    protected function get_account_code($account_id, $payment)
    {
        if ($account_id == 7) {
            $data = MlIncome::where('userid', $this->user_id_manage(session('id')))->where('code', 'penjualan-produk');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlIncome();
                $new->userid = $this->user_id_manage(session('id'));
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
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->where('code', 'persediaan-barang-dagang');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $this->user_id_manage(session('id'));
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persediaan-barang-dagang';
                $new->name = 'Persediaan Barang Dagang';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } 
        elseif ($account_id == 188) {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->where('code', 'persediaan-bahan-baku');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $this->user_id_manage(session('id'));
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persediaan-bahan-baku';
                $new->name = 'Persediaan Bahan Baku';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        }

        elseif ($account_id == 189) {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->where('code', 'persedian-barang-setengah-jadi');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $this->user_id_manage(session('id'));
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persedian-barang-setengah-jadi';
                $new->name = 'Persediaan Barang Setengah jadi';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        }
        
        
        elseif ($account_id == 8) {
            $data = MlCostGoodSold::where('userid', $this->user_id_manage(session('id')))->where('code', 'harga-pokok-penjualan');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCostGoodSold();
                $new->userid = $this->user_id_manage(session('id'));
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
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->where('code', $payment)->first();
            return $data->id;
        } elseif ($account_id == 199) {
            $data = MlIncome::where('userid', $this->user_id_manage(session('id')))->where('code', $payment)->first();
            return $data->id;
        }
    }

    public function get_data_transaction($key = '', $coloum = '')
    {
        $row = DB::table('ml_transaction')->where('id', $key)->first();
        return $row->$coloum;
    }

    public function get_user($key)
    {
        $id = $this->user_id_manage(session('id'));
        $token = session('token');
        $username = $this->username_manage(session('id'));

        $row = DB::table('ml_accounts')->where('id', $id)->where('username', $username)->first();
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

    public function list_account()
    {
        $data['income'] = DB::table('ml_income')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 7)->orderBy('id')->get();
        $data['hpp'] = DB::table('ml_cost_good_sold')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 8)->orderBy('id')->get();
        $data['selling_cost'] = DB::table('ml_selling_cost')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 9)->orderBy('id')->get();
        $data['general_fees'] = DB::table('ml_admin_general_fees')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 10)->orderBy('id')->get();
        $data['non_business_income'] = DB::table('ml_non_business_income')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 11)->orderBy('id')->get();
        $data['non_business_cost'] = DB::table('ml_non_business_expenses')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 12)->orderBy('id')->get();

        return $data;
    }

    public function list_balance_account()
    {
        $data['aktiva_lancar'] = DB::table('ml_current_assets')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 1)->orderBy('id')->get();
        $data['aktiva_tetap'] = DB::table('ml_fixed_assets')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 2)->orderBy('id')->get();
        $data['utang_pendek'] = DB::table('ml_shortterm_debt')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 4)->orderBy('id')->get();
        $data['utang_panjang'] = DB::table('ml_longterm_debt')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 5)->orderBy('id')->get();
        $data['modal'] = DB::table('ml_capital')->where('userid', $this->user_id_manage(session('id')))->where('account_code_id', 6)->orderBy('id')->get();

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

    public function send_to_journal_invoice($transaction_id)
    {
        $invoice = Invoice::findorFail($transaction_id);
        if ($invoice->sync_status !== 1) {
            $untuk = $this->get_account_code(99, $invoice->payment_method);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $this->get_account_code(199, 'pendapatan') . '_' . 7;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $invoice->total_rupiah;
            $cost_time = date('Y-m-d', strtotime($invoice->created));
            $waktu = strtotime($cost_time); 
            $transaction_name = 'Pendapatan ( ' . $invoice->name . ' )';
            $this->sync_journal_invoice($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu);
            
        }
    }

    protected function sync_journal_invoice($transaction_name, $rf, $st, $nominal, $id, $waktu)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'journal_id' => 0,
            'transaction_id' => 1,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(1),
            'created' => $waktu,
            'relasi_trx' => 'invoice_' . $id,
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

        $me = Invoice::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function generate_product_sku($barang) {
        $sku = "";
        $barang_array = explode(" ", $barang);
        foreach($barang_array as $b) {
            if (preg_match('/[A-Za-z]/', $b)) {
                $sku .= substr($b, 0, 1);
            } else {
                $sku .= $b;
            }
            
        }

        $digits = 4;
        $random_number = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $random_letter = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), -2);
        return strtoupper($sku.'-'.$random_number.$random_letter);
    }
}
