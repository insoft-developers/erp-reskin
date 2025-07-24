<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\InterComposeProduct;
use App\Models\InterProduct;
use App\Models\InterPurchase;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MlAdminGeneralFee;
use App\Models\MlCurrentAsset;
use App\Models\MlNonBusinessExpense;
use App\Models\MlSellingCost;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\ProductManufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Traits\CommonTrait;
class ProductManufactureController extends Controller
{
    use CommonTrait;

    /**
     * Display a listing of the resource.
     */

    private $n = 1;
    public function product_manufacture_table()
    {
        $data = ProductManufacture::where('userid', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })
            ->addColumn('product', function ($data) {
                $materials = Product::where('id', $data->product_id);
                if ($materials->count() > 0) {
                    $material = $materials->first();
                    return $material->name . ' - ' . $material->unit;
                } else {
                    return 'product-not-found';
                }
            })
            ->addColumn('total_price', function ($data) {
                $gross_purchase = $data->total_purchase - $data->tax + $data->discount - $data->other_expense;
                return number_format($gross_purchase);
            })
            ->addColumn('tax_other', function ($data) {
                $other = $data->tax - $data->discount + $data->other_expense;
                return number_format($other);
            })
            ->addColumn('final_price', function ($data) {
                return number_format($data->total_purchase);
            })
            ->addColumn('created_at', function ($data) {
                return '<center>' . date('d-m-Y', strtotime($data->transaction_date)) . '</center>';
            })
            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div class="d-flex"><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                }
            })
            ->rawColumns(['action', 'product', 'total_price', 'tax_other', 'final_price', 'created_at', 'sync_status'])
            ->make(true);
    }

    public function index()
    {
        $view = 'product-manufacture';
        $materials = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 2)
            ->where('created_by', 0)
            ->get();
        $accounts = DB::table('ml_current_assets')
            ->where('userid', $this->user_id_manage(session('id')))
            ->get();
        return view('main.product_manufacture', compact('view', 'materials', 'accounts'));
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

        $p_tax = $input['tax'] == null ? 0 : $input['tax'];
        $p_discount = $input['discount'] == null ? 0 : $input['discount'];
        $p_expense = $input['other_expense'] == null ? 0 : $input['other_expense'];
        $p_total = $p_tax + $p_expense - $p_discount;

        if ($p_total < 0) {
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
            'product_id.*' => 'required',
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

        $stock_kurang = 0;
        $lackarray = $input['lack'];

        foreach ($lackarray as $l) {
            $stock_kurang = $stock_kurang + $l;
        }

        if ($stock_kurang > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada komposisi yang kekurangan stock!',
            ]);
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $input['quantity'] = $input['product_count'];
        $input['cost'] = round($input['total_purchase'] / $input['product_count']);
        $transId = ProductManufacture::create($input)->id;
        $this->live_sync($transId);

        $bsj = Product::find($input['product_id']);
        $stock_awal = $bsj->quantity;
        $cost_awal = $bsj->cost;

        $stock_perubahan = $stock_awal + $input['product_count'];
        // $cost_perubahan = (($cost_awal * $stock_awal) + ($input['cost'] * $input['product_count'])) / ($input['product_count'] + $stock_awal);
        $total_stock = $input['product_count'] + $stock_awal;

        if ($total_stock != 0) {
            $cost_perubahan = ($cost_awal * $stock_awal + $input['cost'] * $input['product_count']) / $total_stock;
        } else {
            // Tangani kasus pembagian nol, misalnya:
            $cost_perubahan = $input['cost']; // atau null, atau nilai default lain
        }

        $this->logStock('md_product', $bsj->id, $input['product_count'], 0, $tanggal_transaksi);

        Product::where('id', $input['product_id'])->update([
            'quantity' => $stock_perubahan,
            'cost' => $cost_perubahan,
        ]);

        $this->compositionStockUpdate($transId, $tanggal_transaksi);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function compositionStockUpdate($transactionId, $tanggal)
    {
        $trans = ProductManufacture::findorFail($transactionId);
        $product = Product::findorFail($trans->product_id);
        $coms = ProductComposition::where('product_id', $product->id)->get();
        foreach ($coms as $com) {
            if ($com->product_type == 1) {
                $material = Material::findorFail($com->material_id);
                $stock_awal = $material->stock;
                Material::where('id', $com->material_id)->update([
                    'stock' => $stock_awal - $com->quantity * $trans->quantity,
                ]);

                $stock_out = $com->quantity * $trans->quantity;
                $this->logStock('md_material', $material->id, 0, $stock_out, $tanggal);
            } elseif ($com->product_type == 2) {
                $material = InterProduct::findorFail($com->material_id);
                $stock_awal = $material->stock;
                InterProduct::where('id', $com->material_id)->update([
                    'stock' => $stock_awal - $com->quantity * $trans->quantity,
                ]);

                $stock_out = $com->quantity * $trans->quantity;
                $this->logStock('md_inter_product', $material->id, 0, $stock_out, $tanggal);
            }
        }
    }

    protected function stockCompositionRestore($transactionId)
    {
        $trans = ProductManufacture::findorFail($transactionId);
        $product = Product::findorFail($trans->product_id);
        $coms = ProductComposition::where('product_id', $product->id)->get();
        foreach ($coms as $com) {
            if ($com->product_type == 1) {
                $material = Material::findorFail($com->material_id);
                $stock_awal = $material->stock;
                Material::where('id', $com->material_id)->update([
                    'stock' => $stock_awal + $com->quantity * $trans->quantity,
                ]);

                $stock_out = $com->quantity * $trans->quantity;
                $LogStock = LogStock::where('relation_id', $material->id)
                    ->where('user_id', $this->user_id_manage(session('id')))
                    ->where('table_relation', 'md_material')
                    ->where('stock_out', $stock_out)
                    ->whereDate('created_at', $trans->transaction_date)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($LogStock) {
                    $LogStock->delete();
                }
            } elseif ($com->product_type == 2) {
                $material = InterProduct::findorFail($com->material_id);
                $stock_awal = $material->stock;
                InterProduct::where('id', $com->material_id)->update([
                    'stock' => $stock_awal + $com->quantity * $trans->quantity,
                ]);

                $stock_out = $com->quantity * $trans->quantity;
                $LogStock = LogStock::where('relation_id', $material->id)
                    ->where('user_id', $this->user_id_manage(session('id')))
                    ->where('table_relation', 'md_inter_product')
                    ->where('stock_out', $stock_out)
                    ->whereDate('created_at', $trans->transaction_date)
                    ->orderBy('id', 'desc')
                    ->first();
                if ($LogStock) {
                    $LogStock->delete();
                }
            }
        }
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
        $dt = ProductManufacture::find($id);

      

        if ($dt->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'manufacturing_' . $id)->first();
            if ($journal) {
                JournalList::where('journal_id', $journal->id)->delete();
                Journal::findorFail($journal->id)->delete();
            }
        }

        $purchase = ProductManufacture::find($id);
        $inter = Product::find($purchase->product_id);

        $current_stock = $inter->quantity;
        $current_cost = $inter->cost;

        Product::where('id', $purchase->product_id)->update([
            'quantity' => $current_stock - $purchase->quantity,
            'cost' => $current_stock - $purchase->quantity == 0 ? 0 : $this->reverse_cogs($current_cost, $current_stock, $purchase->cost, $purchase->quantity),
        ]);

        

        

        $LogStock = LogStock::where('relation_id', $inter->id)
            ->where('user_id', $this->user_id_manage(session('id')))
            ->where('table_relation', 'md_product')
            ->where('stock_in', $purchase->quantity)
            ->whereDate('created_at', $purchase->transaction_date)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($LogStock) {
            $LogStock->delete();
        }

        $this->stockCompositionRestore($id);

        ProductManufacture::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function change_manufacture_select(Request $request)
    {
        $input = $request->all();

        $pesanan = $input['product_count'];

        $barang_setengah_jadi = Product::find($input['id']);
        $stok_bsj_sekarang = $barang_setengah_jadi->quantity;

        if ($stok_bsj_sekarang < 0) {
            $stock_mutlak = abs($stok_bsj_sekarang);
            if ($pesanan < $stock_mutlak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok anda saat ini minus ' . $stock_mutlak . '!, Anda harus memasukkan angka jumlah yang diproduksi minimal ' . $stock_mutlak . ' agar transaksi ini dapat diproses...!',
                ]);
            }
        }

        $materials = ProductComposition::where('product_id', $input['id'])->get();
        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-md-4">Nama Bahan</div>';
        $html .= '<div class="col-md-2">COGS</div>';
        $html .= '<div class="col-md-2">Qty Komposisi</div>';
        $html .= '<div class="col-md-2">Qty Produksi</div>';
        $html .= '<div class="col-md-2">Total COGS</div>';
        $html .= '</div>';
        foreach ($materials as $index => $material) {
            if ($material->product_type == 1) {
                $com = Material::findorFail($material->material_id);
                $total_quantity = $material->quantity * $pesanan;
                $total_harga = $total_quantity * $com->cost;

                $html .= '<div class="row mtop10 bariss" id="bariss_' . $index . '">';
                $html .= '<div class="col-md-4">';
                $html .= '<div class="form-group">';
                $html .= '<input value="' . $com->id . '" type="hidden" id="material_id_' . $index . '" name="material_id[]">';
                $html .=
                    '<input readonly value="' .
                    $com->material_name .
                    ' - ' .
                    $com->unit .
                    '" type="text" class="form-control cust-control" id="material_name_' .
                    $index .
                    '"
                                                name="material_name[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    ribuan($com->cost) .
                    '" type="text" class="form-control cust-control"
                                                id="cost_text_' .
                    $index .
                    '" onkeyup="onchange_cost(' .
                    $index .
                    ')"
                                                placeholder="COGS">';
                $html .=
                    '<input value="' .
                    $com->cost .
                    '" class="purchase-amount" type="hidden" id="cost_' .
                    $index .
                    '"
                                                name="cost[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    number_format($material->quantity, 2) .
                    '" type="text"
                                                class="form-control cust-control" id="quantity_text_' .
                    $index .
                    '"
                                                placeholder="Quantity Pembelian">';
                $html .= '<input value="' . $material->quantity . '" type="hidden" id="quantity_' . $index . '" name="quantity[]">';
                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    number_format($total_quantity, 2) .
                    '" type="text"
                                                class="form-control cust-control" id="total_quantity_text_' .
                    $index .
                    '"
                                                placeholder="Quantity Pembelian">';
                // $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]">';

                if ($com->stock < $total_quantity) {
                    $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]"><br><small class="text-danger stock-alert-note">stock tidak cukup! (stock saat ini ' . number_format($com->stock) . ' kurang ' . number_format($total_quantity - $com->stock) . ')</small>';
                    $html .= '<input type="hidden" value="1" name="lack[]">';
                } else {
                    $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]">';
                    $html .= '<input type="hidden" value="0" name="lack[]">';
                }

                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input value="' .
                    ribuan($total_harga) .
                    '" type="text"
                                                class="form-control cust-control" id="total_harga_text_' .
                    $index .
                    '"
                                                placeholder="Total harga" readonly>';
                $html .= '<input class="total-harga" value="' . $total_harga . '" type="hidden" id="total_harga_' . $index . '" name="total_harga[]">';
                $html .= '</div>';
                $html .= '</div>';

                $html .= '</div>';
            } else {
                $com = InterProduct::findorFail($material->material_id);
                $total_quantity = $material->quantity * $pesanan;
                $total_harga = $total_quantity * $com->cost;

                $html .= '<div class="row mtop10 bariss" id="bariss_' . $index . '">';
                $html .= '<div class="col-md-4">';
                $html .= '<div class="form-group">';
                $html .= '<input value="' . $com->id . '" type="hidden" id="material_id_' . $index . '" name="material_id[]">';
                $html .=
                    '<input readonly value="' .
                    $com->product_name .
                    ' - ' .
                    $com->unit .
                    '" type="text" class="form-control cust-control" id="material_name_' .
                    $index .
                    '"
                                                name="material_name[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    ribuan($com->cost) .
                    '" type="text" class="form-control cust-control"
                                                id="cost_text_' .
                    $index .
                    '" onkeyup="onchange_cost(' .
                    $index .
                    ')"
                                                placeholder="COGS">';
                $html .=
                    '<input value="' .
                    $com->cost .
                    '" class="purchase-amount" type="hidden" id="cost_' .
                    $index .
                    '"
                                                name="cost[]">';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    ribuan($material->quantity) .
                    '" type="text"
                                                class="form-control cust-control" id="quantity_text_' .
                    $index .
                    '"
                                                placeholder="Quantity Pembelian">';
                $html .= '<input value="' . $material->quantity . '" type="hidden" id="quantity_' . $index . '" name="quantity[]">';
                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input readonly value="' .
                    ribuan($total_quantity) .
                    '" type="text"
                                                class="form-control cust-control" id="total_quantity_text_' .
                    $index .
                    '"
                                                placeholder="Quantity Pembelian">';
                // $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]">';

                if ($com->stock < $total_quantity) {
                    $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]"><br><small class="text-danger stock-alert-note">stock tidak cukup! (stock saat ini ' . number_format($com->stock) . ' kurang ' . number_format($total_quantity - $com->stock) . ')</small>';
                    $html .= '<input type="hidden" value="1" name="lack[]">';
                } else {
                    $html .= '<input value="' . $total_quantity . '" type="hidden" id="total_quantity_' . $index . '" name="total_quantity[]">';
                    $html .= '<input type="hidden" value="0" name="lack[]">';
                }
                $html .= '</div>';
                $html .= '</div>';

                $html .= '<div class="col-md-2">';
                $html .= '<div class="form-group">';
                $html .=
                    '<input value="' .
                    ribuan($total_harga) .
                    '" type="text"
                                                class="form-control cust-control" id="total_harga_text_' .
                    $index .
                    '"
                                                placeholder="Total harga" readonly>';
                $html .= '<input class="total-harga" value="' . $total_harga . '" type="hidden" id="total_harga_' . $index . '" name="total_harga[]">';
                $html .= '</div>';
                $html .= '</div>';

                $html .= '</div>';
            }
        }

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        $dt = ProductManufacture::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            $untuk = $this->untuk();
            $accode_code_id = 1;
            $keterangan = '';

            $rf = $dt->account_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->total_purchase;
            $waktu = strtotime($dt->transaction_date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);

            $coms = ProductComposition::where('product_id', $dt->product_id)->get();

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

            $this->sync_journal($transaction_name, $rf, $st, $nominal, $input['id'], $waktu, $cost_bahan_baku, $cost_setengah_jadi, $biaya_lain);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function live_sync($id)
    {
        $dt = ProductManufacture::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $untuk = $this->untuk();
            $accode_code_id = 1;
            $keterangan = '';

            $rf = $dt->account_id;
            $st = $untuk . '_' . $accode_code_id;
            $nominal = $dt->total_purchase;
            $waktu = strtotime($dt->transaction_date);
            $transaction_name = $this->get_transaction_name($untuk, $accode_code_id, $keterangan);

            $coms = ProductComposition::where('product_id', $dt->product_id)->get();

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

            $this->sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $cost_bahan_baku, $cost_setengah_jadi, $biaya_lain);
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
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 10) {
            $account = MlAdminGeneralFee::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 12) {
            $account = MlNonBusinessExpense::findorFail($untuk);
            $transaction_name = $account->name;
        } elseif ($accode_code_id == 1) {
            $account = MlCurrentAsset::findorFail($untuk);
            $transaction_name = $account->name;
        }

        return $transaction_name;
    }

    protected function sync_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $cost_baku, $cost_setengah, $biaya_lain)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => $this->user_id_manage(session('id')),
            'journal_id' => 0,
            'transaction_id' => 9,
            'transaction_name' => 'Persediaan Barang Dagang (Manufaktur)',
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => $nominal + $biaya_lain,
            'total_balance' => $nominal + $biaya_lain,
            'color_date' => $this->set_color(9),
            'created' => $waktu,
            'relasi_trx' => 'manufacturing_' . $id,
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
            $fit = $this->get_account_identity('ml_current_assets', 'persediaan-bahan-baku', 1);
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
            $fit = $this->get_account_identity('ml_current_assets', 'persedian-barang-setengah-jadi', 1);
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
            $fit = $this->get_account_identity('ml_non_business_expenses', 'biaya-lain-lain', 12);

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

            $data_list_insert = [
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

            JournalList::insert($data_list_insert);

            $fit = $this->get_account_identity('ml_cost_good_sold', 'harga-pokok-penjualan', 8);
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

        $me = ProductManufacture::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    protected function get_account_identity($table, $code, $aci)
    {
        $names = str_replace('-', ' ', $code);
        $name = ucwords($names);
        $data = DB::table($table)
            ->where('code', $code)
            ->where('userid', $this->user_id_manage(session('id')));

        if ($data->count() > 0) {
            return $data->first();
        } else {
            DB::table($table)->insert([
                'userid' => $this->user_id_manage(session('id')),
                'transaction_id' => 0,
                'account_code_id' => $aci,
                'code' => $code,
                'name' => $name,
                'can_be_deleted' => 1,
                'created' => time(),
            ]);

            $data = DB::table($table)
                ->where('code', $code)
                ->where('userid', $this->user_id_manage(session('id')));
            return $data->first();
        }
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
                'updated_at' => $tanggal,
            ]);

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
