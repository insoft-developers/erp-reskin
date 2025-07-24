<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\AdjustmentCategory;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MdAdjustment;
use App\Models\MdAdjustmentInterProduct;
use App\Models\MdAdjustmentMaterial;
use App\Models\MdAdjustmentProduct;
use App\Models\MdProduct;
use App\Models\MlCapital;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;

class AdjustmentController extends Controller
{
    use CommonTrait;
    public function index()
    {
        $view = 'adjustment';

        return view('main.manage-adjustment.adjustment.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox =
                    '<div class="custom-control custom-checkbox">
                    <input class="custom-control-input checkbox" id="checkbox' .
                    $data->id .
                    '" type="checkbox" value="' .
                    $data->id .
                    '" />
                    <label class="custom-control-label" for="checkbox' .
                    $data->id .
                    '"></label>
                </div>';

                return $checkbox;
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->date)->format('d F Y');
            })
            ->addColumn('total_quantity', function ($data) {
                return $data->total_quantity;
            })
            ->addColumn('detail', function ($data) {
                $list = '<ul>';
                foreach ($data['detail'] as $key => $value) {
                    $name = null;
                    if ($data->type == 'product') {
                        $name = $value->md_product->name ?? null;
                    } elseif ($data->type == 'material') {
                        $name = $value->md_material->material_name ?? null;
                    } elseif ($data->type == 'inter_product') {
                        $name = $value->md_inter_product->product_name ?? null;
                    }
                    $list .= '<li>' . $name . ' @' . $value->quantity . ' (' . ucfirst($value->type) . ')</li>';
                }
                $list .= '</ul>';

                return $list;
            })
            ->addcolumn('sync_status', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong></div>';
                } else {
                    return '<div style="color:red;">Not Sync</div>';
                }
            })

            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    return '<div class="d-flex"><a onclick="unsync('.$data->id.')" title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                } else {
                    return '<div class="d-flex"><a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
                }
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = ['id', 'date', 'total_quantity', 'sync_status', 'user_id', 'category_adjustment_id', 'type'];
        $keyword = $request->keyword;
        $category = $request->category;
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $data = MdAdjustment::orderBy('id', 'desc')
            ->where('user_id', userOwnerId())
            ->select($columns)
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('date', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('date', $tahun);
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category_adjustment_id', $category);
            })
            ->with([
                'md_adjustment_product' => function ($query) use ($keyword, $category) {
                    if ($keyword != '') {
                        $query->whereHas('md_product', function ($q) use ($keyword) {
                            $q->where('name', 'LIKE', '%' . $keyword . '%');
                        });
                    }
                },
            ])
            ->get();

        foreach ($data as $key => $value) {
            if ($value->type == 'product') {
                $value['detail'] = $value->md_adjustment_product ?? [];
            } elseif ($value->type == 'material') {
                $value['detail'] = $value->md_adjustment_material ?? [];
            } elseif ($value->type == 'inter_product') {
                $value['detail'] = $value->md_adjustment_inter_product ?? [];
            } else {
                $value['detail'] = $value->md_adjustment_product ?? [];
            }
        }

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = 'adjustment-create';
        $costGoodSold = $this->costGoodSold();
        $category = AdjustmentCategory::where('account_id', $this->user_id_manage(session('id')));
        if ($category->count() == 0) {
            return redirect()->route('adjustment.category.index')->with('error', 'Anda belum melengkapi data kategori penyesuaian, Silahkan anda lengkapi terlebih dahulu');
        }

        return view('main.manage-adjustment.adjustment.create', compact('view', 'costGoodSold'));
    }

    public function createInterProduct()
    {
        $view = 'adjustment-create';
        $costGoodSold = $this->costGoodSold();
        $category = AdjustmentCategory::where('account_id', $this->user_id_manage(session('id')));
        if ($category->count() == 0) {
            return redirect()->route('adjustment.category.index')->with('error', 'Anda belum melengkapi data kategori penyesuaian, Silahkan anda lengkapi terlebih dahulu');
        }

        return view('main.manage-adjustment.adjustment.create_inter_product', compact('view', 'costGoodSold'));
    }

    public function createMaterial()
    {
        $view = 'adjustment-create';
        $costGoodSold = $this->costGoodSold();
        $category = AdjustmentCategory::where('account_id', $this->user_id_manage(session('id')));
        if ($category->count() == 0) {
            return redirect()->route('adjustment.category.index')->with('error', 'Anda belum melengkapi data kategori penyesuaian, Silahkan anda lengkapi terlebih dahulu');
        }

        return view('main.manage-adjustment.adjustment.create_material', compact('view','category', 'costGoodSold'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $user_id = userOwnerId();
                $tanggal_transaksi = $data['date'].' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => $data['date'],
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'product',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['product_id'] as $key => $value) {
                    $mdAdjustmentProduct = MdAdjustmentProduct::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'product_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'created' => now(),
                        'user_id' => $user_id,
                        'created' => $tanggal_transaksi
                    ]);

                    $product = MdProduct::find($value);
                    $product['quantity'] = $data['type'][$key] == 'addition' ? $product['quantity'] + $data['quantity'][$key] : $product['quantity'] - $data['quantity'][$key];
                    $product->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_product', $value, $stock_in, $stock_out, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id);

                return redirect()->route('adjustment.index')->with('success', 'Data Berhasil di Tambahkan!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function storeInterProduct(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $user_id = userOwnerId();
                $tanggal_transaksi = $data['date'].' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => $data['date'],
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'inter_product',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['md_inter_product_id'] as $key => $value) {
                    $MdAdjustmentInterProduct = MdAdjustmentInterProduct::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'md_inter_product_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'user_id' => $user_id,
                        'created_at' => $tanggal_transaksi
                    ]);

                    $interProduct = InterProduct::find($value);
                    $interProduct['stock'] = $data['type'][$key] == 'addition' ? $interProduct['stock'] + $data['quantity'][$key] : $interProduct['stock'] - $data['quantity'][$key];
                    $interProduct->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_inter_product', $value, $stock_in, $stock_out, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id);

                return redirect()->route('adjustment.index')->with('success', 'Data Berhasil di Tambahkan!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal di Tambahkan!');
        }
    }

    public function storeMaterial(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $user_id = userOwnerId();
                $tanggal_transaksi = $data['date'].' '.date('H:i:s');
                $mdAdjustment = MdAdjustment::create([
                    'date' => $data['date'],
                    'total_quantity' => collect($data['quantity'])->sum(),
                    'created' => now(),
                    'cost_good_sold_id' => $data['cost_good_sold_id'],
                    'user_id' => $user_id,
                    'type' => 'material',
                    'category_adjustment_id' => $data['category_adjustment_id'],
                ]);

                foreach ($data['md_material_id'] as $key => $value) {
                    $MdAdjustmentMaterial = MdAdjustmentMaterial::create([
                        'adjustment_id' => $mdAdjustment->id,
                        'category_adjustment_id' => $data['category_adjustment_id'],
                        'md_material_id' => $value,
                        'quantity' => $data['quantity'][$key],
                        'type' => $data['type'][$key],
                        'user_id' => $user_id,
                        'created_at' => $tanggal_transaksi
                    ]);

                    $material = Material::find($value);
                    $material['stock'] = $data['type'][$key] == 'addition' ? $material['stock'] + $data['quantity'][$key] : $material['stock'] - $data['quantity'][$key];
                    $material->save();

                    $stock_in = $data['type'][$key] == 'addition' ? $data['quantity'][$key] : 0;
                    $stock_out = $data['type'][$key] == 'substraction' ? $data['quantity'][$key] : 0;

                    $this->logStock('md_material', $value, $stock_in, $stock_out, $tanggal_transaksi);
                }

                $this->single_sync_id($mdAdjustment->id);

                return redirect()->route('adjustment.index')->with('success', 'Data Berhasil di Tambahkan!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $data = MdAdjustment::findOrFail($id);
        $view = 'adjustment-edit';

        return view('main.manage-adjustment.category.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = MdAdjustment::findOrFail($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Update!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Update!',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $dt = MdAdjustment::findorFail($id);
                if ($dt->sync_status == 1) {
                    $journal = Journal::where('relasi_trx', 'adjustment_' . $id)->first();
                    JournalList::where('journal_id', $journal->id)->delete();
                    Journal::findorFail($journal->id)->delete();
                }

                $delete = MdAdjustment::find($id);
                
                // $md_adjustment_product = MdAdjustmentProduct::where('adjustment_id', $id)->where('user_id', userOwnerId())->get();

                // // REVERSE QUANTITY
                // foreach ($md_adjustment_product as $key => $value) {
                //     $product = MdProduct::find($value['product_id']);
                //     $product['quantity'] = $value['type'] == 'addition' ? $product['quantity'] - $value['quantity'] : $product['quantity'] + $value['quantity'];
                //     $product->save();

                //     $value->delete();
                // }

                $this->reverseQuantity($delete, $id);

                $delete->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $dt = MdAdjustment::findorFail($id);
                    if ($dt->sync_status == 1) {
                        $journal = Journal::where('relasi_trx', 'adjustment_' . $id)->first();
                        JournalList::where('journal_id', $journal->id)->delete();
                        Journal::findorFail($journal->id)->delete();
                    }

                    $delete = MdAdjustment::find($id);
                    $this->reverseQuantity($delete, $id);

                    $delete->delete();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }

    public function reverseQuantity($delete, $id)
    {
        if ($delete->type == 'product') {

            $md_adjustment_product = MdAdjustmentProduct::where('adjustment_id', $id)->where('user_id', userOwnerId())->get();
            foreach ($md_adjustment_product as $key => $value) {
                $product = MdProduct::find($value['product_id']);
                if (isset($product)) {
                    $product['quantity'] = $value['type'] == 'addition' ? $product['quantity'] - $value['quantity'] : $product['quantity'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->product_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_product')->where('stock_in', $value->quantity)->where('created_at', $value->created)->orderBy('id', 'desc')->first();

                    if($LogStock) {
                        $LogStock->delete();
                    }
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->product_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_product')->where('stock_out', $value->quantity)->where('created_at', $value->created)->orderBy('id', 'desc')->first();
                    if($LogStock) {
                        $LogStock->delete();
                    }
                }


            }
        } elseif ($delete->type == 'inter_product') {
            $md_inter_product = MdAdjustmentInterProduct::where('adjustment_id', $id)->where('user_id', userOwnerId())->get();
            foreach ($md_inter_product as $key => $value) {
                $product = InterProduct::find($value['md_inter_product_id']);
                if (isset($product)) {
                    $product['stock'] = $value['type'] == 'addition' ? $product['stock'] - $value['quantity'] : $product['stock'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->md_inter_product_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_inter_product')->where('stock_in', $value->quantity)->where('created_at', $value->created_at)->orderBy('id', 'desc')->first();
                    if($LogStock) {
                        $LogStock->delete();
                    }
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->md_inter_product_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_inter_product')->where('stock_out', $value->quantity)->where('created_at', $value->created_at)->orderBy('id', 'desc')->first();
                    if($LogStock) {
                        $LogStock->delete();
                    }
                }
            }
        } elseif ($delete->type == 'material') {
            $md_adjustment_material = MdAdjustmentMaterial::where('adjustment_id', $id)->where('user_id', userOwnerId())->get();
            foreach ($md_adjustment_material as $key => $value) {
                $product = Material::find($value['md_material_id']);
                if (isset($product)) {
                    $product['stock'] = $value['type'] == 'addition' ? $product['stock'] - $value['quantity'] : $product['stock'] + $value['quantity'];
                    $product->save();
                }

                $value->delete();
                if ($value->type == 'addition') {
                    $LogStock = LogStock::where('relation_id', $value->md_material_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_material')->where('stock_in', $value->quantity)->where('created_at', $value->created_at)->orderBy('id', 'desc')->first();
                    if($LogStock) {
                        $LogStock->delete();
                    }
                } else if ($value->type == 'substraction') {
                    $LogStock = LogStock::where('relation_id', $value->md_material_id)->where('user_id', $this->user_id_manage(session('id')))->where('table_relation', 'md_material')->where('stock_out', $value->quantity)->where('created_at', $value->created_at)->orderBy('id', 'desc')->first();
                    if($LogStock) {
                        $LogStock->delete();
                    }
                }
            }
        }
    }

    public function sync(Request $request)
    {
        $input = $request->all();

        foreach ($input['ids'] as $id) {
            $dt = MdAdjustment::where('id', $id)->first();
            if ($dt->sync_status !== 1) {
                $untuk = $this->get_code($dt->type);
                $accode_code_id = 1;
                $keterangan = '';
                $rf = $dt->cost_good_sold_id . '_' . $this->asset_code($dt->cost_good_sold_id);
                $st = $untuk . '_' . $accode_code_id;

                

                $tot_nom = 0;
                $items = MdAdjustmentProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Product::findorFail($item->product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }

                $nominal = $tot_nom;
                $waktu = strtotime($dt->date);
                $transaction_name = 'Product Adjustment';
                $this->syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $dt->type);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function single_sync(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $dt = MdAdjustment::where('id', $id)->first();

        $jenis = $dt->type;
        if ($jenis == 'product') {
            $tipe = 'Penyesuaian Produk';
        } elseif ($jenis == 'inter_product') {
            $tipe = 'Penyesuaian Barang Setengah Jadi';
        } elseif ($jenis == 'material') {
            $tipe = 'Penyesuaian Bahan Baku';
        }
        if ($dt->sync_status !== 1) {
            $untuk = $this->get_code($jenis);
            $accode_code_id = 1;
            $keterangan = '';
            $rf = $dt->cost_good_sold_id . '_' . $this->asset_code($dt->cost_good_sold_id);
            $st = $untuk . '_' . $accode_code_id;

            

            $tot_nom = 0;

            if ($jenis == 'product') {
                $items = MdAdjustmentProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Product::findorFail($item->product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'material') {
                $items = MdAdjustmentMaterial::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Material::findorFail($item->md_material_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'inter_product') {
                $items = MdAdjustmentInterProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = InterProduct::findorFail($item->md_inter_product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            $nominal = $tot_nom;
            $waktu = strtotime($dt->date);
            $transaction_name = $tipe;
            $this->syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $dt->type);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function asset_code($asset_id) {
        $cek_cogs = MlCostGoodSold::where('userid', userOwnerId())
        ->where('id', $asset_id)
        ->get();

        if($cek_cogs->count() > 0) {
            return 8;
        } else {
            return 6;
        }
    }

    public function single_sync_id($id)
    {
    
        $dt = MdAdjustment::where('id', $id)->first();

        $jenis = $dt->type;
        if ($jenis == 'product') {
            $tipe = 'Penyesuaian Produk';
        } elseif ($jenis == 'inter_product') {
            $tipe = 'Penyesuaian Barang Setengah Jadi';
        } elseif ($jenis == 'material') {
            $tipe = 'Penyesuaian Bahan Baku';
        }
        if ($dt->sync_status !== 1) {
            $untuk = $this->get_code($jenis);
            $accode_code_id = 1;
            $keterangan = '';

            


            $rf = $dt->cost_good_sold_id . '_' . $this->asset_code($dt->cost_good_sold_id);
            $st = $untuk . '_' . $accode_code_id;

            $tot_nom = 0;

            if ($jenis == 'product') {
                $items = MdAdjustmentProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Product::findorFail($item->product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'material') {
                $items = MdAdjustmentMaterial::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = Material::findorFail($item->md_material_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            else if ($jenis == 'inter_product') {
                $items = MdAdjustmentInterProduct::where('adjustment_id', $id)->get();
                foreach ($items as $item) {
                    $product = InterProduct::findorFail($item->md_inter_product_id);
                    if ($item->type == 'addition') {
                        $angka = $product->cost * $item->quantity;
                    } else {
                        $angka = $product->cost * $item->quantity * -1;
                    }

                    $tot_nom = $tot_nom + $angka;
                }
            }

            $nominal = $tot_nom;
            $waktu = strtotime($dt->date);
            $transaction_name = $tipe;
            $this->syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $dt->type);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    protected function get_code($type)
    {
        $query = MlCurrentAsset::where('userid', userOwnerId());
        if ($type == 'product') {
            $query->where('code', 'persediaan-barang-dagang');
        } elseif ($type == 'inter_product') {
            $query->where('code', 'persedian-barang-setengah-jadi');
        } elseif ($type == 'material') {
            $query->where('code', 'persediaan-bahan-baku');
        }

        $data = $query->first();

        return $data->id ?? null;
    }

    protected function get_account_name($type)
    {
        $query = MlCurrentAsset::where('userid', userOwnerId());
        if ($type == 'product') {
            $query->where('code', 'persediaan-barang-dagang');
        } elseif ($type == 'inter_product') {
            $query->where('code', 'persedian-barang-setengah-jadi');
        } elseif ($type == 'material') {
            $query->where('code', 'persediaan-bahan-baku');
        }

        $data = $query->first();

        return $data->name ?? null;
    }

    protected function get_hpp($id)
    {
        $data = MlCostGoodSold::findorFail($id);
        if (!empty($data)) {
            return $data->name;
        } else {
            return '';
        }
    }

    protected function syncronize_journal($transaction_name, $rf, $st, $nominal, $id, $waktu, $tipe)
    {
        $ex_st = explode('_', $st);
        $ex_rf = explode('_', $rf);

        $data_journal = [
            'userid' => userOwnerId(),
            'journal_id' => 0,
            'transaction_id' => 10,
            'transaction_name' => $transaction_name,
            'rf_accode_id' => $rf,
            'st_accode_id' => $st,
            'nominal' => abs($nominal),
            'total_balance' => abs($nominal),
            'color_date' => $this->set_color(10),
            'created' => $waktu,
            'relasi_trx' => 'adjustment_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        if ($nominal > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $st,
                'account_code_id' => $ex_st[1],
                'asset_data_id' => $ex_st[0],
                'asset_data_name' => $this->get_account_name($tipe),
                'credit' => 0,
                'debet' => abs($nominal),
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
                'asset_data_name' => $this->get_hpp($ex_rf[0]),
                'credit' => abs($nominal),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        } else {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $rf,
                'account_code_id' => $ex_rf[1],
                'asset_data_id' => $ex_rf[0],
                'asset_data_name' => $this->get_hpp($ex_rf[0]),
                'credit' => 0,
                'debet' => abs($nominal),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $st,
                'st_accode_id' => '',
                'account_code_id' => $ex_st[1],
                'asset_data_id' => $ex_st[0],
                'asset_data_name' => $this->get_account_name($tipe),
                'credit' => abs($nominal),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        $me = MdAdjustment::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function costGoodSold()
    {
        $columns = ['id', 'userid', 'transaction_id', 'account_code_id', 'code', 'name', 'can_be_deleted', 'created'];

        // $keyword = $request->keyword;

        $cogs = MlCostGoodSold::orderBy('id', 'desc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->get();
        
        $prive = MlCapital::orderBy('id', 'desc')
            ->where('userid', userOwnerId())
            ->where('code', 'prive')
            ->select($columns)
            ->get();

        $raw = array_merge($cogs->toArray(), $prive->toArray());
        $data = $raw;

        

        foreach ($data as $key => $value) {
            $value['detail'] = $value->md_adjustment_product ?? [];
        }

        

        return $data;
    }

    public function interProduct(Request $request)
    {
        $columns = ['id', 'userid', 'product_name', 'sku', 'category_id', 'cost', 'composition', 'description', 'stock', 'unit', 'min_stock', 'ideal_stock'];

        $keyword = $request->keyword;

        $data = InterProduct::orderBy('id', 'desc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return $data;
    }

    public function material(Request $request)
    {
        $columns = ['id', 'userid', 'material_name', 'sku', 'category_id', 'description', 'supplier_id', 'stock', 'unit', 'cost', 'min_stock', 'ideal_stock'];

        $keyword = $request->keyword;

        $data = Material::orderBy('id', 'desc')
            ->where('userid', userOwnerId())
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->get();

        return $data;
    }

    public function logStock($table, $id, $stock_in, $stock_out, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => userOwnerId(),
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

    public function single_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'adjustment_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            MdAdjustment::where('id', $input['id'])->update([
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
