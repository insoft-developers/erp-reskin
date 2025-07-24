<?php

namespace App\Traits;

use App\Models\InterProduct;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\Material;
use App\Models\MdCustomer;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MlTransaction;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use App\Models\ProductComposition;
use Illuminate\Support\Facades\DB;

trait MobileJournalTrait
{
    public function send_to_journal($transaction_id, $userid)
    {
        $penjualan = Penjualan::findorFail($transaction_id);
        if ($penjualan->sync_status !== 1) {
            $untuk = $this->get_account_code(99, $penjualan->payment_method, $userid);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $this->get_account_code(7, $penjualan->payment_method, $userid) . '_' . 7;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $penjualan->paid;
            $cost_time = date('Y-m-d', strtotime($penjualan->created));
            $waktu = strtotime($cost_time);
            if ($penjualan->customer_id == null) {
                $customer_name = $penjualan->cust_name;
            } else {
                $customer = MdCustomer::findorFail($penjualan->customer_id);
                $customer_name = $customer->name;
            }

            $transaction_name = 'Penjualan ( ' . $customer_name . ' ) ' . $penjualan->reference;
            $this->sync_journal($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu, $userid);
        }
    }

    public function getHpp($id)
    {
        $items = PenjualanProduct::where('penjualan_id', $id)->get();
        $hpp = 0;
        if ($items->count() > 0) {
            foreach ($items as $item) {
                $master = Product::findorFail($item->product_id);
                $cogs = $master->cost * $item->quantity;

                if ($master->created_by == 1) {
                    $kom = ProductComposition::where('product_id', $item->product_id)->get();
                    foreach ($kom as $komposisi) {
                        if ($komposisi->product_type == 1) {
                            $materi = Material::findorFail($komposisi->material_id);
                            $item_cost = $materi->cost * $komposisi->quantity * $item->quantity;
                        } else if ($komposisi->product_type == 2) {
                            $materi = InterProduct::findorFail($komposisi->material_id);
                            $item_cost = $materi->cost * $komposisi->quantity * $item->quantity;
                        }

                        $hpp = $hpp + $item_cost;
                    }
                } else {
                    $hpp = $hpp + $cogs;
                }
            }
        }

        return $hpp;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $akun_hpp = $this->get_account_code(8, '', $userid);
        $akun_barang = $this->get_account_code(1, '', $userid);

        $items = PenjualanProduct::where('penjualan_id', $id)->get();

        $hpp = 0;
        $cost_produk = 0;
        $cost_bahan_baku = 0;
        $cost_setengah_jadi = 0;
        if ($items->count() > 0) {
            foreach ($items as $item) {
                $master = Product::findorFail($item->product_id);
                $cogs = $master->cost * $item->quantity;

                if ($master->created_by == 1) {
                    $kom = ProductComposition::where('product_id', $item->product_id)->get();
                    foreach ($kom as $komposisi) {
                        if ($komposisi->product_type == 1) {
                            $materi = Material::findorFail($komposisi->material_id);
                            $item_cost = $materi->cost * $komposisi->quantity * $item->quantity;
                            $cost_bahan_baku = $cost_bahan_baku + $item_cost;
                        } else if ($komposisi->product_type == 2) {
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
            'userid' => $userid,
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
            'st_accode_id' => $akun_hpp . '_' . 8,
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

        if ($cost_produk > 0) {
            $data_list_insert4 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_barang . '_' . 1,
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


        if ($cost_bahan_baku > 0) {
            $data_list_insert5 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $this->get_account_code(188, '', $userid) . '_' . 1,
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_account_code(188, '', $userid),
                'asset_data_name' => $this->get_transaction_name($this->get_account_code(188, '', $userid), 1, ''),
                'credit' => $cost_bahan_baku,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert5);
        }

        if ($cost_setengah_jadi > 0) {
            $data_list_insert6 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $this->get_account_code(189, '', $userid) . '_' . 1,
                'st_accode_id' => '',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_account_code(189, '', $userid),
                'asset_data_name' => $this->get_transaction_name($this->get_account_code(189, '', $userid), 1, ''),
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
        } elseif ($accode_code_id == 8) {
            $account = MlCostGoodSold::findorFail($untuk);
            $transaction_name = $account->name;
        }
        return $transaction_name;
    }

    protected function get_account_code($account_id, $payment, $userid)
    {
        if ($account_id == 7) {
            $data = MlIncome::where('userid', $userid)->where('code', 'penjualan-produk');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlIncome();
                $new->userid = $userid;
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
            $data = MlCurrentAsset::where('userid', $userid)->where('code', 'persediaan-barang-dagang');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $userid;
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
            $data = MlCostGoodSold::where('userid', $userid)->where('code', 'harga-pokok-penjualan');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCostGoodSold();
                $new->userid = $userid;
                $new->transaction_id = 0;
                $new->account_code_id = 8;
                $new->code = 'harga-pokok-penjualan';
                $new->name = 'Harga Pokok Penjualan';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 188) {
            $data = MlCurrentAsset::where('userid', $userid)->where('code', 'persediaan-bahan-baku');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $userid;
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persediaan-bahan-baku';
                $new->name = 'Persediaan Bahan Baku';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 189) {
            $data = MlCurrentAsset::where('userid', $userid)->where('code', 'persedian-barang-setengah-jadi');
            if ($data->count() > 0) {
                $ids = $data->first();
                return $ids->id;
            } else {
                $new = new MlCurrentAsset();
                $new->userid = $userid;
                $new->transaction_id = 0;
                $new->account_code_id = 1;
                $new->code = 'persedian-barang-setengah-jadi';
                $new->name = 'Persediaan Barang Setengah jadi';
                $new->can_be_deleted = 1;
                $new->created = time();
                $new->save();
                return $new->id;
            }
        } elseif ($account_id == 99) {
            $data = MlCurrentAsset::where('userid', $userid)->where('code', $payment)->first();
            return $data->id;
        } elseif ($account_id == 199) {
            $data = MlIncome::where('userid', $userid)->where('code', $payment)->first();
            return $data->id;
        }
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

    protected function set_color($id)
    {
        $data = MlTransaction::findorFail($id);
        $color = $data->color;
        return $color;
    }

    public function send_to_journal_invoice($transaction_id, $userid = null)
    {
        $user_id = session('id') ?? $userid;
        $invoice = Invoice::findorFail($transaction_id);
        if ($invoice->sync_status !== 1) {
            $untuk = $this->get_account_code(99, $invoice->payment_method, $user_id);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $this->get_account_code(199, 'pendapatan', $user_id) . '_' . 7;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $invoice->total_rupiah;
            $cost_time = date('Y-m-d', strtotime($invoice->created_at));
            $waktu = strtotime($cost_time);
            $transaction_name = 'Pendapatan ( ' . $invoice->name . ' )';
            $this->sync_journal_invoice($transaction_name, $rf, $st, $nominal, $transaction_id, $waktu, $user_id);
        }
    }

    protected function sync_journal_invoice($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
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
}
