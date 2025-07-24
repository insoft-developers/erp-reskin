<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MlCurrentAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlShorttermDebt;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MaterialPurchaseController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;

    public function list(Request $request)
    {
        $userid = $this->user_id_staff($request->userid);
        $query = MaterialPurchase::with('material_purchase_item', 'material_purchase_item.material', 'supplier')->where('userid', $userid);

        if (!empty($request->kata_cari)) {
            $query->whereHas('material_purchase_item.material', function ($q) use ($request) {
                $q->where('material_name', 'LIKE', '%' . $request->kata_cari . '%');
            });
        }

        $query->orderBy('id', 'desc');
        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function type(Request $request)
    {
        $input = $request->all();
        if ($request->payment_type == 1) {
            $pendek = MlShorttermDebt::where('userid', $this->user_id_staff($input['userid']))->get();
            $panjang = MlLongtermDebt::where('userid', $this->user_id_staff($input['userid']))->get();
            $data = $pendek->merge($panjang);
        } else {
            $data = MlCurrentAsset::where('userid', $this->user_id_staff($input['userid']))->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function product(Request $request)
    {
        $input = $request->all();
        $data = Material::where('userid', $this->user_id_staff($input['userid']))->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_staff($request->userid);
        $tanggal_transaksi = date('Y-m-d', strtotime($input['transaction_date'])).' '.date('H:i:s');
        $rules = [
            'transaction_date' => 'required',
            'account_id' => 'required',
            'product_count' => 'required',
            'total_purchase' => 'required',
            'product_id.*' => 'required',
            'purchase_amount.*' => 'required',
            'quantity.*' => 'required',
            'unit_price.*' => 'required',
            'payment_type' => 'required',
        ];

        $validator = Validator::make($input, $rules);
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

        $input['userid'] = $userid;
        $input['transaction_date'] = date('Y-m-d', strtotime($input['transaction_date']));
        $purchase = MaterialPurchase::create($input);
        $purchase_id = $purchase->id;
        $this->live_sync($purchase_id, $userid);
        for ($i = 0; $i < count($input['product_id']); $i++) {
            $item_cost = $this->set_temp_cost($input['total_purchase'], $input['purchase_amount'][$i], $input['tax'], $input['discount'], $input['other_expense'], $input['quantity'][$i], $input['product_count']);
            $item = new MaterialPurchaseItem();
            $item->userid = $userid;
            $item->purchase_id = $purchase_id;
            $item->product_id = $input['product_id'][$i];
            $item->purchase_amount = $input['purchase_amount'][$i];
            $item->quantity = $input['quantity'][$i];
            $item->unit_price = $input['unit_price'][$i];
            $item->cost = $item_cost;
            $item->created_at = $tanggal_transaksi;
            $item->updated_at = $tanggal_transaksi;
            $item->save();

            $mp = Material::findorFail($input['product_id'][$i]);
            $cogs = ($item_cost * $input['quantity'][$i] + $mp->cost * $mp->stock) / ($input['quantity'][$i] + $mp->stock);

            $this->logStock('md_material', $mp->id, $input['quantity'][$i], 0, $userid, $tanggal_transaksi);

            Material::where('id', $input['product_id'][$i])->update([
                'stock' => $mp->stock + $input['quantity'][$i],
                'cost' => round($cogs),
            ]);
        }

        if ($input['payment_type'] == 1) {
            $aids = $input['account_id'];
            $aid = explode('_', $aids);

            $debt = new Debt();
            $debt->debt_from = $aid[0];
            $debt->save_to = $this->untuk($userid);
            $debt->name = 'Persedian Bahan Baku';
            $debt->type = $aid[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
            $debt->sub_type = $aid[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
            $debt->amount = $input['total_purchase'];
            $debt->note = 'Pembelian Bahan Baku Dengan Cara Utang';
            $debt->user_id = $userid;
            $debt->sync_status = 0;
            $debt->relasi_trx = 'material-purchase_' . $purchase_id;
            $debt->created_at = date('Y-m-d H:i:s');
            $debt->updated_at = date('Y-m-d H:i:s');
            $debt->date = $input['transaction_date'];
            $debt->account_code_id = 1;
            $debt->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'id' => $purchase_id,
        ]);
    }

    protected function set_temp_cost($harga_total, $product_price, $tax, $discount, $expense, $quantity, $n)
    {
        // $cost = ($total_price + $tax / $n - $discount / $n + $expense / $n) / $quantity;
        // $rounded = round($cost);
        // return $rounded;

        $harga_sebelum_diskon = $harga_total - $tax + $discount - $expense;
        $tambahan = $tax - $discount + $expense;

        $proporsi = ($product_price / $harga_sebelum_diskon) * $tambahan;
        $subtotal = $product_price;
        $hpp_sementara = ($subtotal + $proporsi) / $quantity;
        $rounded = round($hpp_sementara);

        return $rounded;
    }

    protected function reverse_cogs($cogs, $quantity, $purchase_cogs, $purchase_quantity)
    {
        $old_cost = ($cogs * $quantity - $purchase_cogs * $purchase_quantity) / ($quantity - $purchase_quantity);
        return $old_cost;
    }

    public function sync(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_staff($input['userid']);

        $dt = MaterialPurchase::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            if ($dt->payment_type == 1) {
            } else {
                $untuk = $this->untuk($userid);
                $accode_code_id = 1;
                $keterangan = $dt->reference;

                $rf = $dt->account_id;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $dt->total_purchase;
                $waktu = strtotime($dt->transaction_date);
                $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['id'], $waktu, $userid);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function live_sync($id, $userid)
    {
        $dt = MaterialPurchase::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            if ($dt->payment_type == 1) {
            } else {
                $untuk = $this->untuk($userid);
                $accode_code_id = 1;
                $keterangan = $dt->reference;

                $rf = $dt->account_id;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $dt->total_purchase;
                $waktu = strtotime($dt->transaction_date);
                $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function untuk($userid)
    {
        $data = MlCurrentAsset::where('userid', $userid)->where('code', 'persediaan-bahan-baku')->first();
        return $data->id;
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';

        $account = MlCurrentAsset::findorFail($untuk);
        $transaction_name = $account->name . '(' . $keterangan . ')';

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $userid)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => 9,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal,
            'total_balance' => $nominal,
            'color_date' => $this->set_color(9),
            'created' => $waktu,
            'relasi_trx' => 'material_' . $id,
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

        $me = MaterialPurchase::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function logStock($table, $id, $stock_in, $stock_out, $userid, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => $userid,
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $userid = $this->user_id_staff($request->userid);

        $dt = MaterialPurchase::findorFail($id);

        if ($dt->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'material_' . $id)->first();

            JournalList::where('journal_id', $journal->id)->delete();
            Journal::findorFail($journal->id)->delete();
        }

        $items = MaterialPurchaseItem::where('purchase_id', $id)->get();
        foreach ($items as $item) {
            $product = Material::findorFail($item->product_id);
            $current_stock = $product->stock;
            $current_cost = $product->cost;

            Material::where('id', $item->product_id)->update([
                'stock' => $current_stock - $item->quantity,
                'cost' => $current_stock - $item->quantity == 0 ? 0 : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
            ]);

            $LogStock = LogStock::where('relation_id', $item->product_id)->where('user_id', $userid)->where('table_relation', 'md_material')->where('stock_in', $item->quantity)->whereDate('created_at', $item->created_at)->delete();
        }

        $utang = Debt::where('relasi_trx', 'material-purchase_' . $id);
        if ($utang->count() > 0) {
            $dt = $utang->first();
            if ($dt->sync_status == 1) {
                $journals = Journal::where('relasi_trx', 'utang_' . $dt->id)->get();
                foreach ($journals as $journal) {
                    JournalList::where('journal_id', $journal->id)->delete();
                    Journal::findorFail($journal->id)->delete();
                }
            }

            $delete = Debt::find($dt->id)->delete();

            $dph = DebtPaymentHistory::where('debt_id', $dt->id)->get();
            foreach ($dph as $dh) {
                if ($dh->sync_status == 1) {
                    $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                    foreach ($journals as $journal) {
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }
                }
            }

            $history = DebtPaymentHistory::where('debt_id', $dt->id)->delete();
        }

        MaterialPurchase::destroy($id);
        MaterialPurchaseItem::where('purchase_id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function upload(Request $request)
    {
        $ids = $request->ids;

        $path = storage_path('app/public/images/material_purchase');

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

            $data = MaterialPurchase::findorFail((int) $ids);
            $data->image = $filename;
            $data->save();

            return response()->json(['message' => trans('/storage/test/' . $filename)], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
