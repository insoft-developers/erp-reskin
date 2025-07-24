<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Converse;
use App\Models\ConverseCost;
use App\Models\ConverseItem;
use App\Models\Debt;
use App\Models\DebtPaymentHistory;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MlCurrentAsset;
use App\Models\MlLongtermDebt;
use App\Models\MlSellingCost;
use App\Models\MlShorttermDebt;
use App\Models\Product;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;
use Yajra\DataTables\Facades\DataTables;

class ConverseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use CommonTrait;

    public function converse_table()
    {
        $query = Converse::where('userid', $this->user_id_manage(session('id')));
        $query->when('product_type' == 1, function ($q) {
            $q->with('material');
        });
        $query->when('product_type' == 2, function ($q) {
            $q->with('inter');
        });

        $query->with('converse_item.product');
        $query->with('cost');

        $data = $query->get();
        return DataTables::of($data)
            ->addColumn('item', function ($data) {
                if ($data->cost_account == null) {
                    return '';
                }

                $acost = explode('_', $data->cost_account);
                $account_id = $acost[0];
                $account_code = $acost[1];
                $account_type = $acost[2];

                if ($account_code == 1) {
                    $akun = MlCurrentAsset::find($account_id);
                } elseif ($account_code == 4) {
                    $akun = MlShorttermDebt::find($account_id);
                } elseif ($account_code == 5) {
                    $akun = MlLongtermDebt::find($account_id);
                }

                $html = '';
                if($akun == null) {
                    $html .= '<span style="font-weight:bold;font-size:13px;color:red;"> - </span>';
                } else {
                    $html .= '<span style="font-weight:bold;font-size:13px;color:red;">' . $akun->name . '</span>';
                }
               
                $html .= '<ul>';
                foreach ($data->cost as $dc) {
                    $html .= '<li>' . $dc->nama_biaya . '<br>' . number_format($dc->jumlah_biaya) . '</li>';
                }
                $html .= '</ul>';
                $html .= '<span style="font-weight:bold; font-size:13px;">Total <i class="fa fa-arrow-right"></i> ' . number_format($data->total_biaya) . '</span>';

                return $html;
            })
            ->addColumn('product_quantity', function ($data) {
                return number_format($data->product_quantity);
            })
            ->addColumn('total_material', function ($data) {
                return number_format($data->total_material);
            })
            ->addColumn('total_product', function ($data) {
                return number_format($data->total_product);
            })
            ->addColumn('total_sisa', function ($data) {
                return number_format($data->total_sisa);
            })
            ->addColumn('product_id', function ($data) {
                if ($data->product_type == 1) {
                    $html = '';
                    if($data->material !== null) {
                        $html .= '<div><span style="font-size:13px;font-weight:bold;color:green;">' . $data->material->material_name . ' (' . $data->material->unit . ')</span>' ?? null;
                    } else {
                        $html .= '<div><span style="font-size:13px;font-weight:bold;color:green;"></span>';
                    }
                   
                } elseif ($data->product_type == 2) {
                    $html = '';
                    if($data->inter !== null) {
                        $html .= '<div><span style="font-size:13px;font-weight:bold;color:green;">' . $data->inter->product_name . ' (' . $data->inter->unit . ')</span>' ?? null;
                    } else {
                        $html .= '<div><span style="font-size:13px;font-weight:bold;color:green;"></span>';
                    }
                    
                }
                $html .= '<ul>';
                foreach ($data->converse_item as $c) {
                    if ($c->product_type == 1) {
                        if($c->product == null) {
                            $html .= '<li> - <br>' . number_format($c->quantity) . ' - ' . $c->unit . '</li>';
                        } else {
                            $html .= '<li>' . $c->product->name . '<br>' . number_format($c->quantity) . ' - ' . $c->unit . '</li>';
                        }
                        
                    } else {
                        if($c->inters == null) {
                            $html .= '<li> - <br>' . number_format($c->quantity) . ' - ' . $c->unit . '</li>';
                        } else {
                            $html .= '<li>' . $c->inters->product_name . '<br>' . number_format($c->quantity) . ' - ' . $c->unit . '</li>';
                        }
                       
                    }
                }
                $html .= '</ul>';
                $html .= '</div>';
                return $html;
            })
            ->addColumn('transaction_date', function ($data) {
                return date('d-m-Y', strtotime($data->transaction_date));
            })

            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })

            ->addColumn('product_type', function ($data) {
                return $data->product_type == 1 ? 'Material' : 'Barang 1/2 Jadi';
            })

            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div class="d-flex"><a title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="unsync(' . $data->id . ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                }
            })
            ->rawColumns(['action', 'sync_status', 'item', 'product_id'])
            ->make(true);
    }

    public function index()
    {
        $view = 'konversi';
        $mat = [];
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();

        $products = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('is_manufactured', 1)
            ->get();

        foreach ($materials as $material) {
            $row['id'] = $material->id;
            $row['name'] = $material->material_name;
            $row['type'] = 1;
            $row['stock'] = $material->stock;
            $row['unit'] = $material->unit;
            $row['cost'] = $material->cost;
            array_push($mat, $row);
        }

        $int = [];
        $inters = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();
        foreach ($inters as $inter) {
            $row['id'] = $inter->id;
            $row['name'] = $inter->product_name;
            $row['type'] = 2;
            $row['stock'] = $inter->stock;
            $row['unit'] = $inter->unit;
            $row['cost'] = $inter->cost;
            array_push($int, $row);
        }

        $cek = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->where('code', 'persediaan-barang-sisa');
        if ($cek->count() > 0) {
        } else {
            $data_insert = [
                'userid' => $this->user_id_manage(session('id')),
                'transaction_id' => 0,
                'account_code_id' => 1,
                'code' => 'persediaan-barang-sisa',
                'name' => 'Persediaan Barang Sisa',
                'can_be_deleted' => 1,
                'created' => time(),
            ];
            MlCurrentAsset::create($data_insert);
        }

        $cek2 = MlSellingCost::where('userid', $this->user_id_manage(session('id')))->where('code', 'biaya-produksi');
        if ($cek2->count() > 0) {
        } else {
            $data_insert = [
                'userid' => $this->user_id_manage(session('id')),
                'transaction_id' => 0,
                'account_code_id' => 9,
                'code' => 'biaya-produksi',
                'name' => 'Biaya Produksi',
                'can_be_deleted' => 1,
                'created' => time(),
            ];
            MlSellingCost::create($data_insert);
        }

        $lancar = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))->get();
        $pendek = MlShorttermDebt::where('userid', $this->user_id_manage(session('id')))->get();
        $panjang = MlLongtermDebt::where('userid', $this->user_id_manage(session('id')))->get();

        return view('main.converse', compact('view', 'mat', 'int', 'products', 'inters', 'lancar', 'pendek', 'panjang'));
    }

    public function conversion_selected_item(Request $request)
    {
        $input = $request->all();
        $selected = explode('_', $input['selected']);
        $id = $selected[0];
        $type = $selected[1];

        if ($type == 1) {
            $data = Material::find($id);
        } elseif ($type == 2) {
            $data = InterProduct::find($id);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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
        $arr = explode('_', $input['product_id']);

        try {
            $rules = [
                'transaction_date' => 'required',
                'reference' => 'required',
                'product_id' => 'required',
                'product_quantity' => 'required',
                'unit' => 'required',
                'product_price' => 'required',
                'total_price' => 'required',
                'item.*' => 'required',
                'jumlah.*' => 'required',
                'item_price.*' => 'required',
                'item_total.*' => 'required',
                'total_material' => 'required',
                'total_product' => 'required',
                'total_sisa' => 'required',
                'total_sisa2' => 'required',
            ];

            if ($input['total_biaya'] > 0) {
                $rules['nama_biaya.*'] = 'required';
                $rules['jumlah_biaya.*'] = 'required';
                $rules['cost_account'] = 'required';
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

            if ($input['total_sisa'] < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok Material/Bahan Setengah Jadi yang dikonversi Kurang....!',
                ]);
            }

            $input['userid'] = $this->user_id_manage(session('id'));
            $arr = explode('_', $input['product_id']);

            $input['product_id'] = (int) $arr[0];
            $input['product_type'] = (int) $arr[1];
            $conv = Converse::create($input);
            $this->material_stock_adjust((int) $arr[0], $input['product_quantity'], (int) $arr[1], $tanggal_transaksi);
            $trans_id = $conv->id;
            $this->live_sync($trans_id);

            $items = $input['item'];

            for ($i = 0; $i < count($items); $i++) {
                $it = explode('_', $items[$i]);

                $insert = [
                    'converse_id' => $trans_id,
                    'userid' => $this->user_id_manage(session('id')),
                    'product_id' => $it[0],
                    'product_type' => $it[1],
                    'unit' => $input['unit'],
                    'quantity' => $input['jumlah'][$i],
                    'item_price' => $input['item_price'][$i],
                    'item_total' => $input['item_total'][$i],
                    'created_at' => $tanggal_transaksi,
                    'updated_at' => $tanggal_transaksi
                ];
                ConverseItem::create($insert);
                $this->product_stock_adjust($trans_id, $it[0], $it[1], $input['jumlah'][$i], $input['item_price'][$i], $tanggal_transaksi);
            }

            if ($input['total_biaya'] > 0) {
                $biayas = $input['nama_biaya'];
                for ($i = 0; $i < count($biayas); $i++) {
                    $data_biaya = [
                        'converse_id' => $trans_id,
                        'nama_biaya' => $biayas[$i],
                        'jumlah_biaya' => $input['jumlah_biaya'][$i],
                        'userid' => $this->user_id_manage(session('id')),
                    ];
                    ConverseCost::create($data_biaya);
                }

                $cost_a = explode('_', $input['cost_account']);
                $cost_type = $cost_a[2];

                if ($cost_type == 2) {
                    $debt = new Debt();
                    $debt->debt_from = $cost_a[0];
                    $debt->save_to = $this->get_asset_data_name('biaya', 'id');
                    $debt->name = 'Biaya Produksi Konversi Produk ' . $input['reference'];
                    $debt->type = $cost_a[1] == '4' ? 'Utang Jangka Pendek' : 'Utang Jangka Panjang';
                    $debt->sub_type = $cost_a[1] == '4' ? 'Utang Usaha (Accounts Payable)' : 'Utang Bank Jangka Panjang (Long-term Bank Loans)';
                    $debt->amount = $input['total_biaya'];
                    $debt->note = $input['reference'] == null ? 'Biaya Produksi Konversi Produk Dengan Cara Utang' : 'Biaya Produksi Konversi Produk Dengan Cara Utang (' . $input['reference'] . ')';
                    $debt->user_id = $this->user_id_manage(session('id'));
                    $debt->sync_status = 0;
                    $debt->relasi_trx = 'konversi_' . $trans_id;
                    $debt->created_at = date('Y-m-d H:i:s');
                    $debt->updated_at = date('Y-m-d H:i:s');
                    $debt->date = $input['transaction_date'];
                    $debt->save();

                    $_controller = new DebtController();
                    $_controller->live_sync($debt->id);
                }
            }

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

    protected function product_log_stock($product_id, $quantity, $type, $tanggal)
    {
        $data = [
            'user_id' => $this->user_id_manage(session('id')),
            'relation_id' => $product_id,
            'table_relation' => $type == 1 ? 'md_product' : 'md_inter_product',
            'stock_in' => $quantity,
            'stock_out' => 0,
            'created_at' => $tanggal,
            'updated_at' => $tanggal
        ];

        LogStock::create($data);
    }

    protected function material_log_stock($material_id, $quantity, $type, $tanggal)
    {
        $data = [
            'user_id' => $this->user_id_manage(session('id')),
            'relation_id' => $material_id,
            'table_relation' => $type == 1 ? 'md_material' : 'md_inter_product',
            'stock_in' => 0,
            'stock_out' => $quantity,
            'created_at' => $tanggal,
            'updated_at' => $tanggal
        ];

        LogStock::create($data);
    }

    protected function product_stock_adjust($trans_id, $id, $type, $quantity, $hpp, $tanggal)
    {
        $converse = Converse::find($trans_id);
        $total_biaya = $converse->total_biaya;
        if ($total_biaya > 0) {
            $stok_digunakan = $converse->product_quantity - $converse->total_sisa2;
            $biaya_per_unit = $total_biaya / $stok_digunakan;
            $tambahan = $biaya_per_unit;
            $hpp = $hpp + $tambahan;
        }

        if ($type == 1) {
            $product = Product::find($id);
            $stok_awal = $product->quantity;
            $nilai_awal = $product->cost * $stok_awal;
            $nilai_akhir = $quantity * $hpp;
            $stok_akhir = $stok_awal + $quantity;
            $hpp_akhir = ($nilai_awal + $nilai_akhir) / $stok_akhir;
            $product->quantity = $stok_akhir;
            $product->cost = round($hpp_akhir);
            $product->save();
            $this->product_log_stock($id, $quantity, $type, $tanggal);
        } elseif ($type == 2) {
            $product = InterProduct::find($id);
            $stok_awal = $product->stock;
            $nilai_awal = $product->cost * $stok_awal;
            $nilai_akhir = $quantity * $hpp;
            $stok_akhir = $stok_awal + $quantity;
            $hpp_akhir = ($nilai_awal + $nilai_akhir) / $stok_akhir;
            $product->stock = $stok_akhir;
            $product->cost = round($hpp_akhir);
            $product->save();
            $this->product_log_stock($id, $quantity, $type, $tanggal);
        }
    }

    protected function product_stock_restore($trans_id, $id, $quantity, $hpp, $tanggal, $type)
    {
        $converse = Converse::find($trans_id);
        $total_biaya = $converse->total_biaya;
        if ($total_biaya > 0) {
            $stok_digunakan = $converse->product_quantity - $converse->total_sisa2;
            $biaya_per_unit = $total_biaya / $stok_digunakan;
            $tambahan = $biaya_per_unit;
            $hpp = $hpp + $tambahan;
        }

        if ($type == 1) {
            $table = 'md_product';

            $product = Product::find($id);
            $stok_awal = $product->quantity;
            $nilai_awal = $product->cost * $stok_awal;
            $nilai_akhir = $quantity * $hpp;
            $stok_akhir = $stok_awal - $quantity;
            $hpp_akhir = $stok_akhir == 0 ? 0 : ($nilai_awal - $nilai_akhir) / $stok_akhir;
            $product->quantity = $stok_akhir;
            $product->cost = round($hpp_akhir);
            $product->save();
        } elseif ($type == 2) {
            $table = 'md_inter_product';

            $product = InterProduct::find($id);
            $stok_awal = $product->stock;
            $nilai_awal = $product->cost * $stok_awal;
            $nilai_akhir = $quantity * $hpp;
            $stok_akhir = $stok_awal - $quantity;
            $hpp_akhir = $stok_akhir == 0 ? 0 : ($nilai_awal - $nilai_akhir) / $stok_akhir;
            $product->stock = $stok_akhir;
            $product->cost = round($hpp_akhir);
            $product->save();
        }

        $LogStock = LogStock::where('relation_id', $id)
            ->where('user_id', $this->user_id_manage(session('id')))
            ->where('table_relation', $table)
            ->where('stock_in', $quantity)
            ->where('created_at', $tanggal)
            ->orderBy('id', 'desc')
            ->first();
        $LogStock != null ? $LogStock->delete() : '';
    }

    protected function material_stock_adjust($material_id, $quantity, $type, $tanggal)
    {
        if ($type == 1) {
            $material = Material::find($material_id);
            $stok_awal = $material->stock;
            $material->stock = $stok_awal - $quantity;
            $material->save();
        } elseif ($type == 2) {
            $material = InterProduct::find($material_id);
            $stok_awal = $material->stock;
            $material->stock = $stok_awal - $quantity;
            $material->save();
        }

        $this->material_log_stock($material_id, $quantity, $type, $tanggal);
    }

    protected function material_stock_restore($material_id, $quantity, $type, $tanggal)
    {
        if ($type == 1) {
            $material = Material::find($material_id);
            if($material) {
                $stok_awal = $material->stock;
                $material->stock = $stok_awal + $quantity;
                $material->save();

                
            }
            $relasi = 'md_material';

        } elseif ($type == 2) {
            $material = InterProduct::find($material_id);
            if($material) {
                $stok_awal = $material->stock;
                $material->stock = $stok_awal + $quantity;
                $material->save();

                
            }
            $relasi = 'md_inter_product';
        }

        $LogStock = LogStock::where('relation_id', $material_id)
            ->where('user_id', $this->user_id_manage(session('id')))
            ->where('table_relation', $relasi)
            ->where('stock_out', $quantity)
            ->whereDate('created_at', $tanggal)
            ->orderBy('id', 'desc')
            ->first();
        $LogStock != null ? $LogStock->delete() : '';
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $con = Converse::find($id);
        if ($con->sync_status == 1) {
            $journal = Journal::where('relasi_trx', 'konversi_' . $id);
            if ($journal->count() > 0) {
                JournalList::where('journal_id', $journal->first()->id)->delete();
                Journal::find($journal->first()->id)->delete();
            }
        }
        $this->material_stock_restore($con->product_id, $con->product_quantity, $con->product_type, $con->transaction_date);

        $items = ConverseItem::where('converse_id', $id);
        foreach ($items->get() as $item) {
            $this->product_stock_restore($id, $item->product_id, $item->quantity, $item->item_price, $item->created_at, $item->product_type);
        }

        if ($con->total_biaya > 0 && $con->cost_account != null) {
            $cac = explode('_', $con->cost_account);
            if ($cac[2] == 2) {
                $utang = Debt::where('relasi_trx', 'konversi_' . $id);
                if ($utang->count() > 0) {
                    $dt = $utang->first();
                    if ($dt->sync_status == 1) {
                        $journals = Journal::where('relasi_trx', 'konversi_' . $dt->id)->get();
                        foreach ($journals as $journal) {
                            JournalList::where('journal_id', $journal->id)->delete();
                            Journal::find($journal->id)->delete();
                        }
                    }

                    $delete = Debt::find($dt->id)->delete();

                    $dph = DebtPaymentHistory::where('debt_id', $dt->id)->get();
                    foreach ($dph as $dh) {
                        if ($dh->sync_status == 1) {
                            $journals = Journal::where('relasi_trx', 'payment_' . $dh->id)->get();
                            foreach ($journals as $journal) {
                                JournalList::where('journal_id', $journal->id)->delete();
                                Journal::find($journal->id)->delete();
                            }
                        }
                    }

                    $history = DebtPaymentHistory::where('debt_id', $dt->id)->delete();
                }
            }
        }

        $del = $con->delete();
        if ($del) {
            $items->delete();
            if ($con->total_biaya > 0) {
                ConverseCost::where('converse_id', $id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'failed',
            ]);
        }
    }

    public function live_sync($id)
    {
        $dt = Converse::where('id', $id)->first();
        if ($dt->sync_status !== 1) {
            $transaction_name = 'Konversi Stock ' . $dt->reference;
            $nominal = $dt->total_price;
            $total_jadi = $dt->total_product_jadi;
            $total_setengah = $dt->total_setengah_jadi;
            $t_material = $dt->total_material;
            $t_product = $dt->total_product;
            $sisa = $dt->total_sisa;
            $waktu = strtotime($dt->transaction_date);
            $type = $dt->product_type;
            $total_biaya = $dt->total_biaya;
            $cost_account = $dt->cost_account;
            $this->sync_journal($total_biaya, $cost_account, $transaction_name, $nominal, $total_jadi, $total_setengah, $id, $waktu, $sisa, $type, $t_material, $t_product);
        }
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        $dt = Converse::where('id', $input['id'])->first();
        if ($dt->sync_status !== 1) {
            $transaction_name = 'Konversi Stock ' . $dt->reference;
            $nominal = $dt->total_price;
            $total_jadi = $dt->total_product_jadi;
            $total_setengah = $dt->total_setengah_jadi;
            $t_material = $dt->total_material;
            $t_product = $dt->total_product;
            $sisa = $dt->total_sisa;
            $waktu = strtotime($dt->transaction_date);
            $type = $dt->product_type;
            $total_biaya = $dt->total_biaya;
            $cost_account = $dt->cost_account;
            $this->sync_journal($total_biaya, $cost_account, $transaction_name, $nominal, $total_jadi, $total_setengah, $input['id'], $waktu, $sisa, $type, $t_material, $t_product);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function sync_journal($total_biaya, $cost_account, $transaction_name, $nominal, $total_jadi, $total_setengah, $id, $waktu, $sisa, $type, $total_material, $total_product)
    {
        $st = $this->get_asset_data_name('produk', 'id') . '_1';

        if ($type == 1) {
            $rf = $this->get_asset_data_name('material', 'id') . '_1';
        } else {
            $rf = $this->get_asset_data_name('setengah', 'id') . '_1';
        }

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
            'relasi_trx' => 'konversi_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        if ($total_product > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $st,
                'account_code_id' => 1,
                'asset_data_id' => $this->get_asset_data_name('produk', 'id'),
                'asset_data_name' => $this->get_asset_data_name('produk', 'name'),
                'credit' => 0,
                'debet' => $total_jadi,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);
        }

        if ($total_setengah > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $this->get_asset_data_name('setengah', 'id') . '_1',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_asset_data_name('setengah', 'id'),
                'asset_data_name' => $this->get_asset_data_name('setengah', 'name'),
                'credit' => 0,
                'debet' => $total_setengah,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);
        }

        if ($sisa > 0) {
            $data_list_inserts = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $this->get_asset_data_name('sisa', 'id') . '_1',
                'account_code_id' => 1,
                'asset_data_id' => $this->get_asset_data_name('sisa', 'id'),
                'asset_data_name' => $this->get_asset_data_name('sisa', 'name'),
                'credit' => 0,
                'debet' => $sisa,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_inserts);
        }

        $data_list_insert2 = [
            'journal_id' => $journal_id,
            'rf_accode_id' => $rf,
            'st_accode_id' => '',
            'account_code_id' => 1,
            'asset_data_id' => $type == 1 ? $this->get_asset_data_name('material', 'id') : $this->get_asset_data_name('setengah', 'id'),
            'asset_data_name' => $type == 1 ? $this->get_asset_data_name('material', 'name') : $this->get_asset_data_name('setengah', 'name'),
            'credit' => $total_material,
            'debet' => 0,
            'is_debt' => 0,
            'is_receivables' => 0,
            'created' => $waktu,
        ];

        JournalList::insert($data_list_insert2);

        if ($total_biaya > 0 && $cost_account != null) {
            $cost_a = explode('_', $cost_account);
            if ($cost_a[2] == 1) {
                $data_list_biaya = [
                    'journal_id' => $journal_id,
                    'rf_accode_id' => '',
                    'st_accode_id' => $this->get_asset_data_name('biaya', 'id') . '_9',
                    'account_code_id' => 9,
                    'asset_data_id' => $this->get_asset_data_name('biaya', 'id'),
                    'asset_data_name' => $this->get_asset_data_name('biaya', 'name'),
                    'credit' => 0,
                    'debet' => $total_biaya,
                    'is_debt' => 0,
                    'is_receivables' => 0,
                    'created' => $waktu,
                ];

                JournalList::insert($data_list_biaya);

                $data_list_cost = [
                    'journal_id' => $journal_id,
                    'rf_accode_id' => $this->get_cost_account($cost_account, 'id') . '_' . $this->get_cost_account($cost_account, 'code'),
                    'st_accode_id' => '',
                    'account_code_id' => $this->get_cost_account($cost_account, 'code'),
                    'asset_data_id' => $this->get_cost_account($cost_account, 'id'),
                    'asset_data_name' => $this->get_cost_account($cost_account, 'name'),
                    'credit' => $total_biaya,
                    'debet' => 0,
                    'is_debt' => 0,
                    'is_receivables' => 0,
                    'created' => $waktu,
                ];

                JournalList::insert($data_list_cost);
            }
        }

        $me = Converse::find($id);
        $me->sync_status = 1;
        $me->save();
    }

    protected function get_cost_account($cost_account, $field)
    {
        $ca = explode('_', $cost_account);
        $account_code_id = $ca[1];
        $id = $ca[0];

        if ($account_code_id == 1) {
            $data = MlCurrentAsset::find($id);
        } elseif ($account_code_id == 4) {
            $data = MlShorttermDebt::find($id);
        } elseif ($account_code_id == 5) {
            $data = MlLongtermDebt::find($id);
        }

        if ($field == 'id') {
            return $data->id;
        } elseif ($field == 'name') {
            return $data->name;
        } elseif ($field == 'code') {
            return $data->account_code_id;
        }
    }

    protected function get_asset_data_name($akun, $field)
    {
        if ($akun == 'material') {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'persediaan-bahan-baku')
                ->first();
        } elseif ($akun == 'setengah') {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'persedian-barang-setengah-jadi')
                ->first();
        } elseif ($akun == 'produk') {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'persediaan-barang-dagang')
                ->first();
        } elseif ($akun == 'sisa') {
            $data = MlCurrentAsset::where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'persediaan-barang-sisa')
                ->first();
        } elseif ($akun == 'biaya') {
            $data = MlSellingCost::where('userid', $this->user_id_manage(session('id')))
                ->where('code', 'biaya-produksi')
                ->first();
        }

        if ($field == 'name') {
            return $data->name;
        } else {
            return $data->id;
        }
    }

    public function unsync(Request $request)
    {
        $input = $request->all();
        $code = 'konversi_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            Converse::where('id', $input['id'])->update([
                'sync_status' => 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Unsync journal success!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unsync journal failed!',
            ]);
        }
    }
}
