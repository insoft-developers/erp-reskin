<?php

namespace App\Http\Controllers\Main;

use App\Exports\MaterialPurchaseExport;
use App\Http\Controllers\Controller;
use App\Imports\MaterialPurchaseImport;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseItem;
use App\Models\MlCurrentAsset;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MaterialPurchaseController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */

    private $n = 1;
    public function material_purchase_table()
    {
        $data = MaterialPurchase::where('userid', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    if ($data->payment_type == 1) {
                        return '<div style="color:orange;">Sync from Debt menu</div>';
                    } else {
                        return '<div style="color:red;">Not Sync</div>';
                    }
                }
            })
            ->addColumn('product', function ($data) {
                $h = '';
                $h .= '<table class="table table-bordered">';
                $no = 0;
                $item = MaterialPurchaseItem::where('purchase_id', $data->id)->get();
                foreach ($item as $i) {
                    $no++;
                    $prq = Material::where('id', $i->product_id);
                    if ($prq->count() > 0) {
                        $pr = $prq->first()->material_name;
                    } else {
                        $pr = '';
                    }

                    $h .= '<tr>';
                    $h .= '<td width="2%">' . $no . '</td>';
                    $h .= '<td width="*">' . $pr . '</td>';
                    $h .= '<td width="10%" style="text-align:right;">' . number_format($i->quantity) . '</td>';
                    $h .= '<td width="10%" style="text-align:right;">' . number_format($i->unit_price) . '</td>';
                    $h .= '<td width="10%" style="text-align:right;">' . number_format($i->purchase_amount) . '</td>';
                    $h .= '</tr>';
                }
                $h .= '</table>';
                return $h;
            })
            ->addColumn('total_price', function ($data) {
                $gross_purchase = $data->total_purchase - $data->tax + $data->discount - $data->other_expense;
                return number_format($gross_purchase);
            })
            ->addColumn('tax_other', function ($data) {
                $other = $data->tax - $data->discount + $data->other_expense;
                return '<span style="color:green;font-weight:bold;">' . number_format($other) . '</span><br><small>' . number_format($data->tax) . ' (tax)<br>' . number_format($data->discount) . ' (discount)<br>' . number_format($data->other_expense) . ' (other expense)</small>';
            })
            ->addColumn('final_price', function ($data) {
                return number_format($data->total_purchase);
            })
            ->addColumn('created_at', function ($data) {
                return '<center>' . date('d-m-Y', strtotime($data->transaction_date)) . '</center>';
            })
            ->addColumn('supplier_id', function ($data) {
                return $data->supplier->name ?? null;
            })

            ->addColumn('image', function ($data) {
                if ($data->image != null) {
                    return '<a href="' . Storage::url('images/material_purchase/' . $data->image) . '" target="_blank"><img class="product-images" src="' . Storage::url('images/material_purchase/' . $data->image) . '"></a>';
                } else {
                    return '<img class="product-images" src="' . asset('template/main/images/product-placeholder.png') . '">';
                }
            })
            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div class="d-flex"><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    if ($data->payment_type == 1) {
                        return '<div class="d-flex"><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                    } else {
                        return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                    }
                }
            })
            ->rawColumns(['action', 'product', 'total_price', 'tax_other', 'final_price', 'created_at', 'sync_status', 'image', 'tax_other'])
            ->make(true);
    }

    public function index()
    {
        $view = 'material-purchase';
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $accounts = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();
        $supplier = Supplier::where('userid', $this->user_id_manage(session('id')))->get();
        return view('main.material_purchase', compact('view', 'materials', 'accounts', 'supplier'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $tanggal_transaksi = $input['transaction_date'].' '.date('H:i:s');

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

        $input['image'] = null;

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/material_purchase', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $purchase = MaterialPurchase::create($input);
        $purchase_id = $purchase->id;
        $this->live_sync($purchase_id);
        for ($i = 0; $i < count($input['product_id']); $i++) {
            $item_cost = $this->set_temp_cost($input['total_purchase'], $input['purchase_amount'][$i], $input['tax'], $input['discount'], $input['other_expense'], $input['quantity'][$i], $input['product_count']);
            $item = new MaterialPurchaseItem();
            $item->userid = $this->user_id_manage(session('id'));
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

            $this->logStock('md_material', $mp->id, $input['quantity'][$i], 0, $tanggal_transaksi);

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
            $debt->save_to = $this->untuk();
            $debt->name = 'Persedian Bahan Baku';
            $debt->type = $aid[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
            $debt->sub_type = $aid[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
            $debt->amount = $input['total_purchase'];
            $debt->note = 'Pembelian Bahan Baku Dengan Cara Utang (' . $input['reference'] . ')';
            $debt->user_id = $this->user_id_manage(session('id'));
            $debt->sync_status = 0;
            $debt->relasi_trx = 'material-purchase_' . $purchase_id;
            $debt->created_at = date('Y-m-d H:i:s');
            $debt->updated_at = date('Y-m-d H:i:s');
            $debt->date = $input['transaction_date'];
            $debt->account_code_id = 1;
            $debt->save();

            $_controller = new DebtController();
            $_controller->live_sync($debt->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function set_temp_cost($harga_total, $product_price, $tax, $discount, $expense, $quantity, $n)
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
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    public function reverse_cogs($cogs, $quantity, $purchase_cogs, $purchase_quantity)
    {
        $old_cost = ($cogs * $quantity - $purchase_cogs * $purchase_quantity) / ($quantity - $purchase_quantity);
        return $old_cost;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dt = MaterialPurchase::findorFail($id);

        if ($dt->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'material_' . $id);
            if ($journal->count() > 0) {
                JournalList::where('journal_id', $journal->first()->id)->delete();
                Journal::findorFail($journal->first()->id)->delete();
            }
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

            $LogStock = LogStock::where('relation_id', $item->product_id)
                ->where('user_id', $this->user_id_manage(session('id')))
                ->where('table_relation', 'md_material')
                ->where('stock_in', $item->quantity)
                ->whereDate('created_at', $item->created_at)
                ->orderBy('id', 'desc')
                ->first();

            if ($LogStock) {
                $LogStock->delete();
            }
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

    public function tambah_item()
    {
        $products = Material::where('userid', $this->user_id_manage(session('id')))->get();
        return response()->json([
            'data' => $products,
        ]);
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        $dt = MaterialPurchase::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            if ($dt->payment_type == 1) {
            } else {
                $untuk = $this->untuk();
                $accode_code_id = 1;
                $keterangan = $dt->reference;

                $rf = $dt->account_id;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $dt->total_purchase;
                $waktu = strtotime($dt->transaction_date);
                $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['id'], $waktu);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function live_sync($id)
    {
        $dt = MaterialPurchase::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            if ($dt->payment_type == 1) {
            } else {
                $untuk = $this->untuk();
                $accode_code_id = 1;
                $keterangan = $dt->reference;

                $rf = $dt->account_id;
                $st = $untuk . '_' . $accode_code_id;
                $nominal = $dt->total_purchase;
                $waktu = strtotime($dt->transaction_date);
                $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);
                $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function untuk()
    {
        $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'persediaan-bahan-baku')
            ->first();
        return $data->id;
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';

        $account = MlCurrentAsset::findorFail($untuk);
        $transaction_name = $account->name . ' (' . $keterangan . ')';

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
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

    public function logStock($table, $id, $stock_in, $stock_out, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => $this->user_id_manage(session('id')),
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

    public function download_template_pembelian_material()
    {
        $data = Material::where('userid', $this->user_id_manage(session('id')))->get();
        return Excel::download(new MaterialPurchaseExport($data), 'material_purchase_template.xlsx');
    }

    public function material_purchase_upload(Request $request)
    {
        try {
            $excel = new MaterialPurchaseImport();

            Excel::import($excel, $request->file);
            $purchase_id = $excel->get_purchase_id();
            $total_purchase = $excel->get_total_purchase();
            $product_count = $excel->get_product_count();

            $purchase = MaterialPurchase::findorFail($purchase_id);
            $purchase->product_count = $product_count;
            $purchase->total_purchase = $total_purchase;
            $purchase->save();

            $this->live_sync($purchase_id);

            return response()->json([
                'success' => true,
                'message' => 'success import file',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
