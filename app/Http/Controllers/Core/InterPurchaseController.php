<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\InterComposeProduct;
use App\Models\InterProduct;
use App\Models\InterPurchase;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MlCurrentAsset;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InterPurchaseController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;
    public function list(Request $request)
    {
        $input = $request->all();
        $query = InterPurchase::with('inter_product')->where('userid', $this->user_id_staff($input['userid']));
        if (!empty($input['kata_cari'])) {
            $query->whereHas('inter_product', function ($q) use ($input) {
                $q->where('product_name', 'LIKE', '%' . $input['kata_cari'] . '%');
            });
        }
        $query->orderBy('id', 'desc');
        $data = $query->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function product(Request $request)
    {
        $input = $request->all();
        $data = InterProduct::where('userid', $this->user_id_staff($input['userid']))->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function account(Request $request)
    {
        $input = $request->all();
        $data = MlCurrentAsset::where('userid', $this->user_id_staff($input['userid']))->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function change(Request $request)
    {
        $input = $request->all();

        $rules = [
            'product_count' => 'required',
        ];

        $messages = [
            'product_count.required' => 'Pesanan produk yang dibuat tidak boleh kosong!',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= $nomor . '. ' . str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $pesanan = $input['product_count'];

        $materials = InterComposeProduct::where('inter_product_id', $input['id'])->get();
        $rows = [];
        $grand_total = 0;
        foreach ($materials as $index => $material) {
            if ($material->product_type == 1) {
                $com = Material::findorFail($material->material_id);
                $total_quantity = $material->quantity * $pesanan;
                $total_harga = $total_quantity * $com->cost;
                $grand_total = $grand_total + $total_harga;

                $row['material_name'] = $com->material_name . ' - ' . $com->unit;
                $row['cost'] = $com->cost;
                $row['quantity'] = $material->quantity;
                $row['total_quantity'] = $total_quantity;
                $row['total_price'] = $total_harga;

                array_push($rows, $row);
            } else {
                $com = InterProduct::findorFail($material->material_id);
                $total_quantity = $material->quantity * $pesanan;
                $total_harga = $total_quantity * $com->cost;
                $grand_total = $grand_total + $total_harga;

                $row['material_name'] = $com->product_name . ' - ' . $com->unit;
                $row['cost'] = $com->cost;
                $row['quantity'] = $material->quantity;
                $row['total_quantity'] = $total_quantity;
                $row['total_price'] = $total_harga;

                array_push($rows, $row);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
            'grand_total' => $grand_total,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $tanggal_transaksi = date('Y-m-d', strtotime($input['transaction_date'])).' '.date('H:i:s');
        $p_tax = $input['tax'] == null ? 0 : $input['tax'];
        $p_discount = $input['discount'] == null ? 0 : $input['discount'];
        $p_expense = $input['other_expense'] == null ? 0 : $input['other_expense'];
        $p_total = $p_tax + $p_expense - $p_discount;

        if($p_total < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Diskon tidak boleh lebih besar dari Pajak ditambah Biaya Lain lain...!',
            ]);
        }

        $rules = [
            'transaction_date' => 'required',
            'account_id' => 'required',
            'product_count' => 'required',
            'total_purchase' => 'required',
            'product_id' => 'required',
        ];

        $messages = [
            'total_purchase.required' => 'Data sudah terisi klik simpan',
        ];

        $validator = Validator::make($input, $rules, $messages);
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

        $kurang_stok = [];
        $kom = InterComposeProduct::where('inter_product_id', $input['product_id'])->get();
        foreach ($kom as $k) {
            if ($k->product_type == 1) {
                $material = Material::findorFail($k->material_id);
                $qty_needed = $k->quantity * $input['product_count'];
                $stock = $material->stock;
                $selisih = $qty_needed - $stock;
                if ($qty_needed > $stock) {
                    $pesan = 'Stok ' . $material->material_name . ' kurang ' . number_format($selisih) . '. Stock saat ini ' . number_format($stock) . ' yang dibutuhkan ' . number_format($qty_needed);
                    array_push($kurang_stok, $pesan);
                }
            } else {
                $material = InterProduct::findorFail($k->material_id);
                $qty_needed = $k->quantity * $input['product_count'];
                $stock = $material->stock;
                $selisih = $qty_needed - $stock;
                if ($qty_needed > $stock) {
                    $pesan = 'Stok ' . $material->product_name . ' kurang ' . number_format($selisih) . '. Stock saat ini ' . number_format($stock) . ' yang dibutuhkan ' . number_format($qty_needed);
                    array_push($kurang_stok, $pesan);
                }
            }
        }

        if (count($kurang_stok) > 0) {
            $html = '';
            $nomor = 0;
            foreach ($kurang_stok as $p) {
                $nomor++;
                $html .= $nomor . '. ' . $p . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $userid = $this->user_id_staff($request->userid);
        $input['userid'] = $userid;
        $input['quantity'] = $input['product_count'];
        $input['cost'] = round($input['total_purchase'] / $input['product_count']);
        $input['transaction_date'] = date('Y-m-d', strtotime($input['transaction_date']));
        $transId = InterPurchase::create($input)->id;
        $this->live_sync($transId, $userid);

        $bsj = InterProduct::findorFail($input['product_id']);
        $stock_awal = $bsj->stock;
        $cost_awal = $bsj->cost;

        $stock_perubahan = $stock_awal + $input['product_count'];
        $cost_perubahan = ($cost_awal * $stock_awal + $input['cost'] * $input['product_count']) / ($input['product_count'] + $stock_awal);

        $this->logStock('md_inter_product', $bsj->id, $input['product_count'], 0, $userid, $transId, $tanggal_transaksi);

        InterProduct::where('id', $input['product_id'])->update([
            'stock' => $stock_perubahan,
            'cost' => $cost_perubahan,
        ]);

        $this->compositionStockUpdate($transId, $tanggal_transaksi);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function live_sync($id, $userid)
    {
        $dt = InterPurchase::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $this->untuk($userid);

            $accode_code_id = 1;
            $keterangan = '';

            $rf = $dt->account_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->total_purchase;
            $waktu = strtotime($dt->transaction_date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);

            $coms = InterComposeProduct::where('inter_product_id', $dt->product_id)->get();

            $cost_bahan_baku = 0;
            $cost_setengah_jadi = 0;
            foreach ($coms as $com) {
                if ($com->product_type == 1) {
                    $material = Material::findorFail($com->material_id);
                    $new_cost = $material->cost * $com->quantity * $dt->quantity;
                    $cost_bahan_baku = $cost_bahan_baku + $new_cost;
                } elseif ($com->product_type == 2) {
                    $material = InterProduct::findorFail($com->material_id);
                    $new_cost = $material->cost * $com->quantity * $dt->quantity;
                    $cost_setengah_jadi = $cost_setengah_jadi + $new_cost;
                }
            }

            $biaya_lain = $dt->tax + $dt->other_expense - $dt->discount;

            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $cost_bahan_baku, $cost_setengah_jadi, $biaya_lain, $userid);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function untuk($userid)
    {
        $data = MlCurrentAsset::where('userid', $this->user_id_staff($userid))->where('code', 'persedian-barang-setengah-jadi')->first();
        return $data->id;
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        $account = MlCurrentAsset::findorFail($untuk);
        $transaction_name = $account->name;

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $cost_baku, $cost_setengah, $biaya_lain, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $this->user_id_staff($userid),
            'journal_id' => 0,
            'transaction_id' => 9,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal + $biaya_lain,
            'total_balance' => $nominal + $biaya_lain,
            'color_date' => $this->set_color(9),
            'created' => $waktu,
            'relasi_trx' => 'interpurchase_' . $id,
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

        if ($cost_baku > 0) {
            $fit = $this->get_account_identity('ml_current_assets', 'persediaan-bahan-baku', 1, $userid);
            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $fit->id . '_' . $fit->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $fit->account_code_id,
                'asset_data_id' => $fit->id,
                'asset_data_name' => $fit->name,
                'credit' => $cost_baku,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        if ($cost_setengah > 0) {
            $fit = $this->get_account_identity('ml_current_assets', 'persedian-barang-setengah-jadi', 1, $userid);
            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $fit->id . '_' . $fit->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $fit->account_code_id,
                'asset_data_id' => $fit->id,
                'asset_data_name' => $fit->name,
                'credit' => $cost_setengah,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        if ($biaya_lain > 0) {
            $fit = $this->get_account_identity('ml_non_business_expenses', 'biaya-lain-lain', 12, $userid);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $fit->id . '_' . $fit->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $fit->account_code_id,
                'asset_data_id' => $fit->id,
                'asset_data_name' => $fit->name,
                'credit' => 0,
                'debet' => $biaya_lain,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $rf,
                'st_accode_id' => '',
                'account_code_id' => $ex_rf[1],
                'asset_data_id' => $ex_rf[0],
                'asset_data_name' => $this->get_transaction_name($ex_rf[0], $ex_rf[1], ''),
                'credit' => $biaya_lain,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);

            $fit = $this->get_account_identity('ml_cost_good_sold', 'harga-pokok-penjualan', 8, $userid);
            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $fit->id . '_' . $fit->account_code_id,
                'account_code_id' => $fit->account_code_id,
                'asset_data_id' => $fit->id,
                'asset_data_name' => $fit->name,
                'credit' => $biaya_lain,
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        $me = InterPurchase::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    protected function logStock($table, $id, $stock_in, $stock_out, $userid, $transactionid, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => $this->user_id_staff($userid),
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'relasi_trx' => 'interproduct_' . $transactionid,
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    protected function get_account_identity($table, $code, $aci, $userid)
    {
        $names = str_replace('-', ' ', $code);
        $name = ucwords($names);
        $data = DB::table($table)->where('code', $code)->where('userid', $this->user_id_staff($userid));

        if ($data->count() > 0) {
            return $data->first();
        } else {
            DB::table($table)->insert([
                'userid' => $this->user_id_staff($userid),
                'transaction_id' => 0,
                'account_code_id' => $aci,
                'code' => $code,
                'name' => $name,
                'can_be_deleted' => 1,
                'created' => time(),
            ]);

            $data = DB::table($table)->where('code', $code)->where('userid', $this->user_id_staff($userid));
            return $data->first();
        }
    }

    protected function compositionStockUpdate($transactionId, $tanggal)
    {
        $trans = InterPurchase::findorFail($transactionId);
        $product = InterProduct::findorFail($trans->product_id);
        $coms = InterComposeProduct::where('inter_product_id', $product->id)->get();
        foreach ($coms as $com) {
            $stock_out = $com->quantity * $trans->quantity;
            if ($com->product_type == 1) {
                $material = Material::findorFail($com->material_id);
                $stock_awal = $material->stock;
                Material::where('id', $com->material_id)->update([
                    'stock' => $stock_awal - $stock_out,
                ]);
                $this->logStock('md_material', $com->material_id, 0, $stock_out, $trans->userid, $transactionId, $tanggal);
            } elseif ($com->product_type == 2) {
                $material = InterProduct::findorFail($com->material_id);
                $stock_awal = $material->stock;
                InterProduct::where('id', $com->material_id)->update([
                    'stock' => $stock_awal - $stock_out,
                ]);
                $this->logStock('md_inter_product', $com->material_id, 0, $stock_out, $trans->userid, $transactionId, $tanggal);
            }
        }
    }

    protected function stockCompositionRestore($transactionId)
    {
        $trans = InterPurchase::findorFail($transactionId);
        $product = InterProduct::findorFail($trans->product_id);
        $coms = InterComposeProduct::where('inter_product_id', $product->id)->get();
        foreach ($coms as $com) {
            if ($com->product_type == 1) {
                $material = Material::findorFail($com->material_id);
                $stock_awal = $material->stock;
                Material::where('id', $com->material_id)->update([
                    'stock' => $stock_awal + $com->quantity * $trans->quantity,
                ]);
            } elseif ($com->product_type == 2) {
                $material = InterProduct::findorFail($com->material_id);
                $stock_awal = $material->stock;
                InterProduct::where('id', $com->material_id)->update([
                    'stock' => $stock_awal + $com->quantity * $trans->quantity,
                ]);
            }
        }
    }

    public function sync(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_staff($input['userid']);

        $dt = InterPurchase::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            $untuk = $this->untuk($userid);
            $accode_code_id = 1;
            $keterangan = '';

            $rf = $dt->account_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->total_purchase;
            $waktu = strtotime($dt->transaction_date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);

            $coms = InterComposeProduct::where('inter_product_id', $dt->product_id)->get();

            $cost_bahan_baku = 0;
            $cost_setengah_jadi = 0;
            foreach ($coms as $com) {
                if ($com->product_type == 1) {
                    $material = Material::findorFail($com->material_id);
                    $new_cost = $material->cost * $com->quantity * $dt->quantity;
                    $cost_bahan_baku = $cost_bahan_baku + $new_cost;
                } elseif ($com->product_type == 2) {
                    $material = InterProduct::findorFail($com->material_id);
                    $new_cost = $material->cost * $com->quantity * $dt->quantity;
                    $cost_setengah_jadi = $cost_setengah_jadi + $new_cost;
                }
            }

            $biaya_lain = $dt->tax + $dt->other_expense - $dt->discount;

            $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['id'], $waktu, $cost_bahan_baku, $cost_setengah_jadi, $biaya_lain, $userid);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $userid = $request->userid;

        $dt = InterPurchase::findorFail($id);

        if ($dt->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'interpurchase_' . $id)->first();

            JournalList::where('journal_id', $journal->id)->delete();
            Journal::findorFail($journal->id)->delete();
        }

        $purchase = InterPurchase::findorFail($id);
        $inter = InterProduct::findorFail($purchase->product_id);

        $current_stock = $inter->stock;
        $current_cost = $inter->cost;

        InterProduct::where('id', $purchase->product_id)->update([
            'stock' => $current_stock - $purchase->quantity,
            'cost' => $current_stock - $purchase->quantity == 0 ? 0 : $this->reverse_cogs($current_cost, $current_stock, $purchase->cost, $purchase->quantity),
        ]);

        $this->stockCompositionRestore($id);
        InterPurchase::destroy($id);

        $stockdelete = LogStock::where('relasi_trx', 'interproduct_' . $id)->delete();

        if (!$stockdelete) {
            $LogStock = LogStock::where('relation_id', $purchase->product_id)->where('user_id', $this->user_id_staff($userid))->where('table_relation', 'md_inter_product')->where('stock_in', $purchase->quantity)->whereDate('created_at', $purchase->created_at)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function reverse_cogs($cogs, $quantity, $purchase_cogs, $purchase_quantity)
    {
        $old_cost = ($cogs * $quantity - $purchase_cogs * $purchase_quantity) / ($quantity - $purchase_quantity);
        return $old_cost;
    }
}
