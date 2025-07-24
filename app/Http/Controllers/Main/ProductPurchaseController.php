<?php

namespace App\Http\Controllers\Main;

use App\Exports\ProductPurchaseExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductPurchaseImport;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlNonBusinessExpense;
use App\Models\MlSellingCost;
use App\Models\MlShorttermDebt;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;
use Yajra\DataTables\DataTables;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use CommonTrait;

    private $n = 1;
    public function product_purchase_table()
    {
        $data = ProductPurchase::where('userid', $this->user_id_manage(session('id')))->get();
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
                $item = ProductPurchaseItem::where('purchase_id', $data->id)->get();
                foreach ($item as $i) {
                    $no++;
                    $pr = Product::find($i->product_id);
                    $h .= '<tr>';
                    $h .= '<td width="2%">' . $no . '</td>';
                    $h .= '<td width="*">' . $pr->name . '</td>';
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
                    return '<a href="' . Storage::url('images/product_purchase/' . $data->image) . '" target="_blank"><img class="product-images" src="' . Storage::url('images/product_purchase/' . $data->image) . '"></a>';
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
            ->rawColumns(['action', 'product', 'total_price', 'tax_other', 'final_price', 'created_at', 'sync_status', 'supplier_id', 'image'])
            ->make(true);
    }

    public function index()
    {
        $view = 'product-purchase';
        $products = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 1)
            ->where('buffered_stock', 1)
            ->get();
        $accounts = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();
        $supplier = Supplier::where('userid', $this->user_id_manage(session('id')))->get();
        return view('main.product_purchase', compact('view', 'products', 'accounts', 'supplier'));
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
        $tanggal_transaksi = $input['transaction_date'] . ' ' . date('H:i:s');

        $rules = [
            'supplier_id' => 'required',
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
            $path = Storage::putFileAs('public/images/product_purchase', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $purchase = ProductPurchase::create($input);
        $purchase_id = $purchase->id;
        $this->live_sync($purchase_id);
        for ($i = 0; $i < count($input['product_id']); $i++) {
            $item_cost = $this->set_temp_cost($input['total_purchase'], $input['purchase_amount'][$i], $input['tax'], $input['discount'], $input['other_expense'], $input['quantity'][$i], $input['product_count']);
            $item = new ProductPurchaseItem();
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

            $mp = Product::findorFail($input['product_id'][$i]);

            $denominator = $input['quantity'][$i] + $mp->quantity;
            $cogs = $denominator != 0 ? (($item_cost * $input['quantity'][$i] + $mp->cost * $mp->quantity) / $denominator) : 0;


            $this->logStock('md_product', $mp->id, $input['quantity'][$i], 0, $tanggal_transaksi);

            Product::where('id', $input['product_id'][$i])->update([
                'quantity' => $mp->quantity + $input['quantity'][$i],
                'cost' => round($cogs),
            ]);
        }

        if ($input['payment_type'] == 1) {
            $aids = $input['account_id'];
            $aid = explode('_', $aids);

            $debt = new Debt();
            $debt->debt_from = $aid[0];
            $debt->save_to = $this->untuk();
            $debt->name = 'Persedian Barang Dagang';
            $debt->type = $aid[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
            $debt->sub_type = $aid[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
            $debt->amount = $input['total_purchase'];
            $debt->note = $input['reference'] == null ? 'Pembelian Barang Dengan Cara Utang' : 'Pembelian Barang Dengan Cara Utang (' . $input['reference'] . ')';
            $debt->user_id = $this->user_id_manage(session('id'));
            $debt->sync_status = 0;
            $debt->relasi_trx = 'product-purchase_' . $purchase_id;
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
    public function edit(string $id)
    {
        $purchase = ProductPurchase::findorFail($id);
        $detail = ProductPurchaseItem::where('purchase_id', $id)->get();
        $data['purchase'] = $purchase;
        $data['detail'] = $detail;
        $products = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 1)
            ->get();

        $n = 1;

        foreach ($detail as $index => $d) {
            if ($index > 0) {
                $n++;
                $html = '';
                $html .= '<div class="row mtop10 bariss tambahan" id="bariss_' . $n . '">';
                $html .= '<div class="col-md-4">';
                $html .= '<div class="form-group">';
                $html .= '<select class="form-control cust-control select-item" id="product_id_' . $n . '" name="product_id[]">';
                $html .= '<option value="">Pilih Produk</option>';
                foreach ($products as $product) {
                    if ($product->id == $d->product_id) {
                        $html .= '<option selected value="' . $product->id . '">' . $product->name . '-' . $product->unit . '</option>';
                    } else {
                        $html .= '<option value="' . $product->id . '">' . $product->name . '-' . $product->unit . '</option>';
                    }
                }
                $html .= '</select>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-3">';
                $html .= '<div class="form-group">';
                $html .= '<input value="' . ribuan($d->purchase_amount) . '" type="text" class="form-control cust-control" id="purchase_amount_text_' . $n . '" onkeyup="onchange_purchase_amount(' . $n . ')"placeholder = "Harga total pembelian" > ';
                $html .= '<input value="' . $d->purchase_amount . '" class="purchase-amount" type="hidden" id="purchase_amount_' . $n . '" name="purchase_amount[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .= '<input value="' . ribuan($d->quantity) . '" onkeyup="onchange_quantity(' . $n . ')" type="text" class = "form-control cust-control" id = "quantity_text_' . $n . '" placeholder = "Quantity Pembelian" > ';
                $html .= '<input value="' . $d->quantity . '" type="hidden" id="quantity_' . $n . '" name="quantity[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .= '<input value="' . ribuan($d->unit_price) . '" onkeyup="onchange_unit_price(' . $n . ')" type="text" class = "form-control cust-control" id = "unit_price_text_' . $n . '" placeholder = "Harga Satuan" readonly> ';
                $html .= '<input value="' . $d->unit_price . '" type="hidden" id="unit_price_' . $n . '" name="unit_price[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-1 button-product-action">';
                $html .= '<center><a title="Tambah Produk" href="javascript:void(0);" onclick="tambah_item()" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown"data-bs-auto-close="outside"><i class="fa fa-plus"></i></a></center>';
                $html .= '<center><a style="margin-left:5px;" title="Hapus Produk" href="javascript:void(0);" onclick ="hapus_item(' . $n . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle = "dropdown" data-bs-auto-close = "outside"> <i class="fa fa-trash"></i></a></center>';
                $html .= '</div>';

                $html .= '</div>';
            }
        }
        $data['html'] = $html;
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();

        $tanggal_transaksi = $input['transaction_date'] . ' ' . date('H:i:s');
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

        $input['userid'] = $this->user_id_manage(session('id'));
        $purchase = ProductPurchase::findorFail($id);
        $purchase->update($input);

        $current_item = ProductPurchaseItem::where('purchase_id', $id)->get();
        foreach ($current_item as $ci) {
            $mdproduct = Product::findorFail($ci->product_id);
            Product::where('id', $ci->product_id)->update([
                'quantity' => $mdproduct->quantity - $ci->quantity,
                'cost' => $this->reverse_cogs($mdproduct->cost, $mdproduct->quantity, $ci->cost, $ci->quantity),
            ]);
        }

        ProductPurchaseItem::where('purchase_id', $id)->delete();

        $purchase_id = $id;
        for ($i = 0; $i < count($input['product_id']); $i++) {
            $item_cost = $this->set_temp_cost($input['total_price'], $input['purchase_amount'][$i], $input['tax'], $input['discount'], $input['other_expense'], $input['quantity'][$i], $input['product_count']);
            $item = new ProductPurchaseItem();
            $item->userid = $this->user_id_manage(session('id'));
            $item->purchase_id = $purchase_id;
            $item->product_id = $input['product_id'][$i];
            $item->purchase_amount = $input['purchase_amount'][$i];
            $item->quantity = $input['quantity'][$i];
            $item->unit_price = $input['unit_price'][$i];
            $item->cost = $item_cost;
            $item->save();

            $mp = Product::findorFail($input['product_id'][$i]);
            $cogs = ($item_cost * $input['quantity'][$i] + $mp->cost * $mp->quantity) / ($input['quantity'][$i] + $mp->quantity);
            $this->logStock('md_product', $mp->id, $input['quantity'][$i], 0, $tanggal_transaksi);

            Product::where('id', $input['product_id'][$i])->update([
                'quantity' => $mp->quantity + $input['quantity'][$i],
                'cost' => round($cogs),
            ]);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dt = ProductPurchase::findorFail($id);

        if ($dt->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'purchasing_' . $id);
            if ($journal->count() > 0) {
                JournalList::where('journal_id', $journal->first()->id)->delete();
                Journal::findorFail($journal->first()->id)->delete();
            }
        }
        $items = ProductPurchaseItem::where('purchase_id', $id)->get();
        foreach ($items as $item) {
            $product = Product::findorFail($item->product_id);
            $current_stock = $product->quantity;
            $current_cost = $product->cost;

            Product::where('id', $item->product_id)->update([
                'quantity' => $current_stock - $item->quantity,
                'cost' => $current_stock - $item->quantity == 0 ? $product->default_cost : $this->reverse_cogs($current_cost, $current_stock, $item->cost, $item->quantity),
            ]);

            $LogStock = LogStock::where('relation_id', $product->id)
                ->where('user_id', $this->user_id_manage(session('id')))
                ->where('table_relation', 'md_product')
                ->where('stock_in', $item->quantity)
                ->whereDate('created_at', $item->created_at)
                ->orderBy('id', 'desc')
                ->first();
            $LogStock != null ? $LogStock->delete() : '';
        }

        $utang = Debt::where('relasi_trx', 'product-purchase_' . $id);
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

        ProductPurchase::destroy($id);
        ProductPurchaseItem::where('purchase_id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function tambah_item()
    {
        $products = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 1)
            ->where('buffered_stock', 1)
            ->get();
        return response()->json([
            'data' => $products,
        ]);
    }

    public function live_sync($id)
    {
        $dt = ProductPurchase::where('id', $id)->first();
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

    public function sync(Request $request)
    {
        $input = $request->all();

        $dt = ProductPurchase::where('id', $input['id'])->first();
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

    protected function untuk()
    {
        $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
            ->where('code', 'persediaan-barang-dagang')
            ->first();
        return $data->id;
    }

    protected function get_transaction_name($untuk, $accode_code_id, $keterangan)
    {
        $transaction_name = '';
        if ($accode_code_id == 9) {
            $account = MlSellingCost::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 10) {
            $account = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 12) {
            $account = MlNonBusinessExpense::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name . ' (' . $keterangan . ')';
        }

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
            'relasi_trx' => 'purchasing_' . $id,
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

        $me = ProductPurchase::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function productPurchaseType(Request $request)
    {
        if ($request->payment_type == 1) {
            $pendek = MlShorttermDebt::where('userid', $this->user_id_manage(session('id')))->get();
            $panjang = MlLongtermDebt::where('userid', $this->user_id_manage(session('id')))->get();
            $data = $pendek->merge($panjang);
        } else {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->get();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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

    public function download_template_pembelian()
    {
        $data = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 1)
            ->get();
        return Excel::download(new ProductPurchaseExport($data), 'product_purchase_template.xlsx');
    }

    public function product_purchase_upload(Request $request)
    {
        try {
            $excel = new ProductPurchaseImport();

            Excel::import($excel, $request->file);
            $purchase_id = $excel->get_purchase_id();
            $total_purchase = $excel->get_total_purchase();
            $product_count = $excel->get_product_count();

            $purchase = ProductPurchase::findorFail($purchase_id);
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
