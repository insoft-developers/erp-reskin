<?php

namespace App\Http\Controllers\Main;

use App\Exports\ReportStockExport;
use App\Http\Controllers\Controller;
use App\Models\InterComposeProduct;
use App\Models\InterProduct;
use App\Models\InterPurchase;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MaterialPurchaseItem;
use App\Models\MdAdjustmentInterProduct;
use App\Models\MdAdjustmentMaterial;
use App\Models\MdAdjustmentProduct;
use App\Models\MdProduct;
use App\Models\PenjualanProduct;
use App\Models\ProductPurchaseItem;
use App\Models\TransferStockMaterial;
use App\Models\TransferStockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use function Laravel\Prompts\select;

class LaporanStockController extends Controller
{
    public function index(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $view = 'laporan-pajak';

        return view('main.report.stock.index', compact('view', 'userKey'));
    }

    public function data(Request $request)
    {
        $category = $request->category;
        if ($category == 'barang-jadi') {
            $data = $this->getDataBarangJadi($request);
        } elseif ($category == 'manufaktur') {
            $data = $this->getDataManufaktur($request);
        } elseif ($category == 'setengah-jadi') {
            $data = $this->getDataSetBarangJadi($request);
        } elseif ($category == 'bahan-baku') {
            $data = $this->getDataMaterial($request);
        }

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data['name'];
            })
            ->addColumn('initial_stock', function ($data) {
                return $data['initial_stock'];
            })
            ->addColumn('total_in', function ($data) {
                return $data['total_in'];
            })
            ->addColumn('total_out', function ($data) {
                return $data['total_out'];
            })
            ->addColumn('final_stock', function ($data) {
                return $data['final_stock'];
            })
            ->addColumn('unit_price', function ($data) {
                return number_format($data['unit_price'], 0, ',', '.');
            })
            ->addColumn('stock_value', function ($data) {
                return number_format($data['stock_value'], 0, ',', '.');
            })
            ->addColumn('stock_list', function ($data) {
                if($data['stock_list'] == $data['final_stock']) {
                    return '<div style="font-weight:bold;color:green;">'.number_format($data['stock_list']).'</div>';
                } else {
                    return '<div style="font-weight:bold;color:red;">'.number_format($data['stock_list']).'</div>';
                }
                
            })
            ->addColumn('selisih', function ($data) {
                $selisih = $data['stock_list'] - $data['final_stock'];
                return number_format($selisih, 0, ',', '.');
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataBarangJadi(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $keyword = $request->keyword ?? null;
        $columns = ['id', 'name', 'cost', 'buffered_stock', 'created_by', 'is_manufactured', 'user_id','quantity'];

        $product = MdProduct::where('is_manufactured', 1)
                    ->select($columns)
                    ->where('buffered_stock', 1)
                    ->whereNot('created_by', 1)
                    ->where('user_id', $this->get_owner_id(session('id')))
                    ->when($keyword, function ($query) use ($keyword) {
                        if ($keyword != '') {
                            return $query->where('name', 'like', '%'.$keyword.'%');
                        }
                    })
                    ->get();

        $result = [];
        foreach ($product as $key => $value) {
            $transactions = LogStock::where('table_relation', 'md_product')
            ->where('relation_id', $value->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
            $total_in = $transactions->sum('stock_in');
            $total_out = $transactions->sum('stock_out');

            $name = $value->name;
            $unit_price = $value->cost;

            // $before_month = LogStock::where('table_relation', 'md_product')
            //     ->where('relation_id', $value->id)
            //     ->whereMonth('created_at', (int)$month-1)
            //     ->whereYear('created_at', $year)
            //     ->get();
            $stock_last_year = 0;
            if ($month == 1) {
                $before_month = LogStock::where('table_relation', 'md_product')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 12) // Bulan Desember
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_akhir_bulan_december = LogStock::where('table_relation', 'md_product')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 11) // Bulan November
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_last_year = $stock_akhir_bulan_december->sum('stock_in') - $stock_akhir_bulan_december->sum('stock_out');
            } else {
                $before_month = LogStock::where('table_relation', 'md_product')
                    ->where('relation_id', $value->id)
                    ->when($month, function ($query) use ($month) {
                        if ($month == now()->format('m')) {
                            $query->whereMonth('created_at', '!=', now()->month);
                        } else {
                            $query->whereMonth('created_at', '<', $month);
                        }
                    })
                    ->when($year, function ($query) use ($year) {
                        if ($year != now()->year) {
                            $query->whereYear('created_at', $year);
                        }
                    })
                    // ->whereYear('created_at', $year)
                    ->get();
            }

            $stock_in_init = $before_month->sum('stock_in');
            $stock_out_init = $before_month->sum('stock_out');
            $initial_stock = ($month != 1) ? $stock_in_init - $stock_out_init : $stock_last_year + ($stock_in_init - $stock_out_init);
                
            $stock_final = $initial_stock;
            $stock_final += $total_in - $total_out;
            $stock_value = $stock_final * $unit_price;

            $result[] = [
                'name' => $name,
                'initial_stock' => $initial_stock,
                'total_in' => $total_in,
                'total_out' => $total_out,
                'final_stock' => $stock_final,
                'unit_price' => $unit_price,
                'stock_value' => $stock_value,
                'stock_list' => $value->quantity
            ];
        }

        return $result;
    }

    public function getDataManufaktur(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $keyword = $request->keyword ?? null;
        $columns = ['id', 'name', 'cost', 'buffered_stock', 'created_by', 'is_manufactured', 'user_id','quantity'];

        $product = MdProduct::where('is_manufactured', 2)
                            ->where('buffered_stock', 1)
                            ->whereNot('created_by', 1)
                            ->where('user_id', $this->get_owner_id(session('id')))
                            ->select($columns)
                            ->when($keyword, function ($query) use ($keyword) {
                                if ($keyword != '') {
                                    return $query->where('name', 'like', '%'.$keyword.'%');
                                }
                            })
                            ->get();
        $result = [];
        foreach ($product as $key => $value) {
            $transactions = LogStock::where('table_relation', 'md_product')
            ->where('relation_id', $value->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
            $total_in = $transactions->sum('stock_in');
            $total_out = $transactions->sum('stock_out');

            $name = $value->name;
            $unit_price = $value->cost;

            // $before_month = LogStock::where('table_relation', 'md_product')
            //     ->where('relation_id', $value->id)
            //     ->whereMonth('created_at', (int)$month-1)
            //     ->whereYear('created_at', $year)
            //     ->get();
            $stock_last_year = 0;
            if ($month == 1) {
                $before_month = LogStock::where('table_relation', 'md_product')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 12) // Bulan Desember
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                    $stock_akhir_bulan_december = LogStock::where('table_relation', 'md_product')
                        ->where('relation_id', $value->id)
                        ->whereMonth('created_at', 11) // Bulan November
                        ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                        ->get();

                    $stock_last_year = $stock_akhir_bulan_december->sum('stock_in') - $stock_akhir_bulan_december->sum('stock_out');
            } else {
                $before_month = LogStock::where('table_relation', 'md_product')
                    ->where('relation_id', $value->id)
                    // ->whereMonth('created_at', (int)$month - 1)
                    ->when($month, function ($query) use ($month) {
                        if ($month == now()->format('m')) {
                            $query->whereMonth('created_at', '!=', now()->month);
                        } else {
                            $query->whereMonth('created_at', '<', $month);
                        }
                    })
                    ->when($year, function ($query) use ($year) {
                        if ($year != now()->year) {
                            $query->whereYear('created_at', $year);
                        }
                    })
                    // ->whereYear('created_at', $year)
                    ->get();
            }

            $stock_in_init = $before_month->sum('stock_in');
            $stock_out_init = $before_month->sum('stock_out');
            $initial_stock = ($month != 1) ? $stock_in_init - $stock_out_init : $stock_last_year + ($stock_in_init - $stock_out_init);
                
            $stock_final = $initial_stock;
            $stock_final += $total_in - $total_out;
            $stock_value = $stock_final * $unit_price;

            $result[] = [
                'name' => $name,
                'initial_stock' => $initial_stock,
                'total_in' => $total_in,
                'total_out' => $total_out,
                'final_stock' => $stock_final,
                'unit_price' => $unit_price,
                'stock_value' => $stock_value,
                'stock_list' => $value->quantity
            ];
        }

        return $result;
    }

    public function getDataSetBarangJadi(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $keyword = $request->keyword ?? null;
        $columns = ['id', 'product_name', 'cost', 'userid', 'stock'];

        $product = InterProduct::where('userid', $this->get_owner_id(session('id')))
                                ->select($columns)
                                ->when($keyword, function ($query) use ($keyword) {
                                    if ($keyword != '') {
                                        return $query->where('product_name', 'like', '%'.$keyword.'%');
                                    }
                                })
                                ->get();
        $result = [];
        foreach ($product as $key => $value) {
            $transactions = LogStock::where('table_relation', 'md_inter_product')
            ->where('relation_id', $value->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
            $total_in = $transactions->sum('stock_in');
            $total_out = $transactions->sum('stock_out');

            $name = $value->product_name;
            $unit_price = $value->cost;

            // $before_month = LogStock::where('table_relation', 'md_inter_product')
            //     ->where('relation_id', $value->id)
            //     ->whereMonth('created_at', (int)$month-1)
            //     ->whereYear('created_at', $year)
            //     ->get();
            $stock_last_year = 0;
            if ($month == 1) {
                $before_month = LogStock::where('table_relation', 'md_inter_product')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 12) // Bulan Desember
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_akhir_bulan_december = LogStock::where('table_relation', 'md_inter_product')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 11) // Bulan November
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_last_year = $stock_akhir_bulan_december->sum('stock_in') - $stock_akhir_bulan_december->sum('stock_out');
            } else {
                $before_month = LogStock::where('table_relation', 'md_inter_product')
                    ->where('relation_id', $value->id)
                    // ->whereMonth('created_at', (int)$month - 1)
                    ->when($month, function ($query) use ($month) {
                        if ($month == now()->format('m')) {
                            $query->whereMonth('created_at', '!=', now()->month);
                        } else {
                            $query->whereMonth('created_at', '<', $month);
                        }
                    })
                    ->when($year, function ($query) use ($year) {
                        if ($year != now()->year) {
                            $query->whereYear('created_at', $year);
                        }
                    })
                    // ->whereYear('created_at', $year)
                    ->get();
            }

            $stock_in_init = $before_month->sum('stock_in');
            $stock_out_init = $before_month->sum('stock_out');
            $initial_stock = ($month != 1) ? $stock_in_init - $stock_out_init : $stock_last_year + ($stock_in_init - $stock_out_init);

            $stock_final = $initial_stock;
            $stock_final += $total_in - $total_out;
            $stock_value = $stock_final * $unit_price;

            $result[] = [
                'name' => $name,
                'initial_stock' => $initial_stock,
                'total_in' => $total_in,
                'total_out' => $total_out,
                'final_stock' => $stock_final,
                'unit_price' => $unit_price,
                'stock_value' => $stock_value,
                'stock_list' => $value->stock
            ];
        }

        return $result;
    }

    public function getDataMaterial(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $keyword = $request->keyword ?? null;
        $columns = ['id', 'material_name', 'cost', 'userid','stock'];

        $product = Material::where('userid', $this->get_owner_id(session('id')))
                                ->select($columns)
                                ->when($keyword, function ($query) use ($keyword) {
                                    if ($keyword != '') {
                                        return $query->where('material_name', 'like', '%'.$keyword.'%');
                                    }
                                })
                                ->get();

        $result = [];
        foreach ($product as $key => $value) {
            $transactions = LogStock::where('table_relation', 'md_material')
            ->where('relation_id', $value->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
            $total_in = $transactions->sum('stock_in');
            $total_out = $transactions->sum('stock_out');

            $name = $value->material_name;
            $unit_price = $value->cost;

            // $before_month = LogStock::where('table_relation', 'md_material')
            //     ->where('relation_id', $value->id)
            //     ->whereMonth('created_at', (int)$month-1)
            //     ->whereYear('created_at', $year)
            //     ->get();
            $stock_last_year = 0;

            if ($month == 1) {
                $before_month = LogStock::where('table_relation', 'md_material')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 12) // Bulan Desember
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_akhir_bulan_december = LogStock::where('table_relation', 'md_material')
                    ->where('relation_id', $value->id)
                    ->whereMonth('created_at', 11) // Bulan November
                    ->whereYear('created_at', $year - 1) // Tahun sebelumnya
                    ->get();

                $stock_last_year = $stock_akhir_bulan_december->sum('stock_in') - $stock_akhir_bulan_december->sum('stock_out');
            } else {
                $before_month = LogStock::where('table_relation', 'md_material')
                    ->where('relation_id', $value->id)
                    // ->whereMonth('created_at', (int)$month - 1)
                    ->when($month, function ($query) use ($month) {
                        if ($month == now()->format('m')) {
                            $query->whereMonth('created_at', '!=', now()->month);
                        } else {
                            $query->whereMonth('created_at', '<', $month);
                        }
                    })
                    ->when($year, function ($query) use ($year) {
                        if ($year != now()->year) {
                            $query->whereYear('created_at', $year);
                        }
                    })
                    ->get();
            }

            $stock_in_init = $before_month->sum('stock_in');
            $stock_out_init = $before_month->sum('stock_out');
            $initial_stock = ($month != 1) ? $stock_in_init - $stock_out_init : $stock_last_year + ($stock_in_init - $stock_out_init);
                
            $stock_final = $initial_stock;
            $stock_final += $total_in - $total_out;
            $stock_value = $stock_final * $unit_price;

            $result[] = [
                'name' => $name,
                'initial_stock' => $initial_stock,
                'total_in' => $total_in,
                'total_out' => $total_out,
                'final_stock' => $stock_final,
                'unit_price' => $unit_price,
                'stock_value' => $stock_value,
                'stock_list' => $value->stock
            ];
        }

        return $result;
    }

    public function export(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $categories = [
            'barang-jadi',
            'manufaktur',
            'setengah-jadi',
            'bahan-baku',
        ];

        $data = [];

        foreach ($categories as $category) {
            if ($category == 'barang-jadi') {
                $result = $this->getDataBarangJadi($request);
            } elseif ($category == 'manufaktur') {
                $result = $this->getDataManufaktur($request);
            } elseif ($category == 'setengah-jadi') {
                $result = $this->getDataSetBarangJadi($request);
            } elseif ($category == 'bahan-baku') {
                $result = $this->getDataMaterial($request);
            }

            $data[] = $result;
        }
        $dateName = Carbon::create($year, $month, 1)->locale('id')->isoFormat('MMMM YYYY');

        return Excel::download(new ReportStockExport($data), "Laporan Stock $dateName.xlsx");
    }

    public function syncStock(Request $request)
    {
        $this->syncStockBarangJadi($request);
        // $this->syncStockManufaktur($request);
        $this->syncStockSetengahJadi($request);
        $this->syncStockMaterial($request);

        return response()->json(['status' => true, 'message' => 'Singkronisasi Berhasil']);
    }

    public function syncStockBarangJadi(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $logStockCompare = [];

        $product_id = MdProduct::where('user_id', $this->get_owner_id(session('id')))->where('is_manufactured', 1)->where('buffered_stock', 1)->pluck('id');

        $PenjualanProduct = PenjualanProduct::whereIn('product_id', $product_id)
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('product_id', 'quantity', 'created')
                            ->get();

        foreach ($PenjualanProduct as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'created_at' => $value->created,
                'type' => 'stock_out',
            ];
        }
                            
        $MdAdjustmentProduct = MdAdjustmentProduct::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('product_id', 'quantity', 'created', 'type', 'user_id')
                            ->get();

        foreach ($MdAdjustmentProduct as $key => $value) {
            if ($value->type == 'addition') {
                $logStockCompare[] = [
                    'product_id' => $value->product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created,
                    'type' => 'stock_in',
                ];
            } else if ($value->type == 'substraction') {
                $logStockCompare[] = [
                    'product_id' => $value->product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created,
                    'type' => 'stock_out',
                ];
            }
            
        }

        $ProductPurchaseItem = ProductPurchaseItem::where('userid', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('product_id', 'quantity', 'created_at', 'userid')
                            ->get();

        foreach ($ProductPurchaseItem as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $TransferStockProduct = TransferStockProduct::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('product_from_id', 'stock_from', 'product_to_id', 'stock_to', 'created_at', 'user_id')
                            ->get();

        foreach ($TransferStockProduct as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_from_id,
                'quantity' => $value->stock_from,
                'created_at' => $value->created_at,
                'type' => 'stock_out',
            ];
            $logStockCompare[] = [
                'product_id' => $value->product_to_id,
                'quantity' => $value->stock_to,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $idExist = [];
        foreach ($logStockCompare as $key => $value) {
            if ($value['type'] == 'stock_in') {
                $stock_in = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_product')->where('stock_in', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_in['id'])) {
                    $idExist[] = $stock_in['id'];
                }
            } else if ($value['type'] == 'stock_out') {
                $stock_out = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_product')->where('stock_out', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_out['id'])) {
                    $idExist[] = $stock_out['id'];
                }
            }
        }

        $deleteLogStock = LogStock::where('table_relation', 'md_product')->where('user_id', $this->get_owner_id(session('id')))->whereNotIn('id', $idExist)->delete();

        return true;
    }

    public function syncStockManufaktur(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $logStockCompare = [];

        $product_id = MdProduct::where('user_id', $this->get_owner_id(session('id')))->where('is_manufactured', 2)->where('buffered_stock', 1)->pluck('id');

        $PenjualanProduct = PenjualanProduct::whereIn('product_id', $product_id)
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('product_id', 'quantity', 'created')
                            ->get();

        foreach ($PenjualanProduct as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'created_at' => $value->created,
                'type' => 'stock_out',
            ];
        }
                            
        $MdAdjustmentProduct = MdAdjustmentProduct::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('product_id', 'quantity', 'created', 'type', 'user_id')
                            ->get();

        foreach ($MdAdjustmentProduct as $key => $value) {
            if ($value->type == 'addition') {
                $logStockCompare[] = [
                    'product_id' => $value->product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created,
                    'type' => 'stock_in',
                ];
            } else if ($value->type == 'substraction') {
                $logStockCompare[] = [
                    'product_id' => $value->product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created,
                    'type' => 'stock_out',
                ];
            }
            
        }

        $coms = InterComposeProduct::whereIn('inter_product_id', $product_id)->get();
        // foreach ($coms as $com) {
        //     if ($com->product_type == 1) {
        //         $material = Material::findorFail($com->material_id);
        //         $stock_awal = $material->stock;
        //         Material::where('id', $com->material_id)->update([
        //             'stock' => $stock_awal - $com->quantity * $trans->quantity,
        //         ]);
                
        //         $stock_out = $com->quantity * $trans->quantity;
        //         $this->logStock('md_material', $material->id, 0, $stock_out);
        //     } elseif ($com->product_type == 2) {
        //         $material = InterProduct::findorFail($com->material_id);
        //         $stock_awal = $material->stock;
        //         InterProduct::where('id', $com->material_id)->update([
        //             'stock' => $stock_awal - $com->quantity * $trans->quantity,
        //         ]);
                
        //         $stock_out = $com->quantity * $trans->quantity;
        //         $this->logStock('md_inter_product', $material->id, 0, $stock_out);
        //     }
        // }

        $TransferStockProduct = TransferStockProduct::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('product_from_id', 'stock_from', 'product_to_id', 'stock_to', 'created_at', 'user_id')
                            ->get();

        foreach ($TransferStockProduct as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_from_id,
                'quantity' => $value->stock_from,
                'created_at' => $value->created_at,
                'type' => 'stock_out',
            ];
            $logStockCompare[] = [
                'product_id' => $value->product_to_id,
                'quantity' => $value->stock_to,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $idExist = [];
        foreach ($logStockCompare as $key => $value) {
            if ($value['type'] == 'stock_in') {
                $stock_in = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_product')->where('stock_in', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_in['id'])) {
                    $idExist[] = $stock_in['id'];
                }
            } else if ($value['type'] == 'stock_out') {
                $stock_out = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_product')->where('stock_out', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_out['id'])) {
                    $idExist[] = $stock_out['id'];
                }
            }
        }

        $deleteLogStock = LogStock::where('table_relation', 'md_product')->where('user_id', $this->get_owner_id(session('id')))->whereNotIn('id', $idExist)->delete();

        return true;
    }

    public function syncStockSetengahJadi(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $logStockCompare = [];

        $product_id = InterProduct::where('userid', $this->get_owner_id(session('id')))->pluck('id');                            
        $MdAdjustmentProduct = MdAdjustmentInterProduct::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('md_inter_product_id', 'quantity', 'created_at', 'type', 'user_id')
                            ->get();

        foreach ($MdAdjustmentProduct as $key => $value) {
            if ($value->type == 'addition') {
                $logStockCompare[] = [
                    'product_id' => $value->md_inter_product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created_at,
                    'type' => 'stock_in',
                ];
            } else if ($value->type == 'substraction') {
                $logStockCompare[] = [
                    'product_id' => $value->md_inter_product_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created_at,
                    'type' => 'stock_out',
                ];
            }
        }

        $ProductPurchaseItem = InterPurchase::where('userid', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('product_id', 'quantity', 'created_at', 'userid')
                            ->get();

        foreach ($ProductPurchaseItem as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $idExist = [];
        foreach ($logStockCompare as $key => $value) {
            if ($value['type'] == 'stock_in') {
                $stock_in = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_inter_product')->where('stock_in', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_in['id'])) {
                    $idExist[] = $stock_in['id'];
                }
            } else if ($value['type'] == 'stock_out') {
                $stock_out = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_inter_product')->where('stock_out', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_out['id'])) {
                    $idExist[] = $stock_out['id'];
                }
            }
        }

        $deleteLogStock = LogStock::where('table_relation', 'md_inter_product')->where('user_id', $this->get_owner_id(session('id')))->whereNotIn('id', $idExist)->delete();

        return true;
    }

    public function syncStockMaterial(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $logStockCompare = [];

        $MdAdjustmentMaterial = MdAdjustmentMaterial::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created', $month)
                            // ->whereYear('created', $year)
                            ->select('md_material_id', 'quantity', 'created_at', 'type', 'user_id')
                            ->get();

        foreach ($MdAdjustmentMaterial as $key => $value) {
            if ($value->type == 'addition') {
                $logStockCompare[] = [
                    'product_id' => $value->md_material_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created_at,
                    'type' => 'stock_in',
                ];
            } else if ($value->type == 'substraction') {
                $logStockCompare[] = [
                    'product_id' => $value->md_material_id,
                    'quantity' => $value->quantity,
                    'created_at' => $value->created_at,
                    'type' => 'stock_out',
                ];
            }
        }

        $ProductPurchaseItem = MaterialPurchaseItem::where('userid', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('product_id', 'quantity', 'created_at', 'userid')
                            ->get();

        foreach ($ProductPurchaseItem as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->product_id,
                'quantity' => $value->quantity,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $TransferStockProduct = TransferStockMaterial::where('user_id', $this->get_owner_id(session('id')))
                            // ->whereMonth('created_at', $month)
                            // ->whereYear('created_at', $year)
                            ->select('material_from_id', 'stock_from', 'material_to_id', 'stock_to', 'created_at', 'user_id')
                            ->get();

        foreach ($TransferStockProduct as $key => $value) {
            $logStockCompare[] = [
                'product_id' => $value->material_from_id,
                'quantity' => $value->stock_from,
                'created_at' => $value->created_at,
                'type' => 'stock_out',
            ];
            $logStockCompare[] = [
                'product_id' => $value->material_to_id,
                'quantity' => $value->stock_to,
                'created_at' => $value->created_at,
                'type' => 'stock_in',
            ];
        }

        $idExist = [];
        foreach ($logStockCompare as $key => $value) {
            if ($value['type'] == 'stock_in') {
                $stock_in = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_material')->where('stock_in', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_in['id'])) {
                    $idExist[] = $stock_in['id'];
                }
            } else if ($value['type'] == 'stock_out') {
                $stock_out = LogStock::where('relation_id', $value['product_id'])->where('user_id', $this->get_owner_id(session('id')))->where('table_relation', 'md_material')->where('stock_out', $value['quantity'])->whereDate('created_at', Carbon::parse($value['created_at']))->orderBy('id', 'desc')->first();
                if (isset($stock_out['id'])) {
                    $idExist[] = $stock_out['id'];
                }
            }
        }

        $deleteLogStock = LogStock::where('table_relation', 'md_material')->where('user_id', $this->get_owner_id(session('id')))->whereNotIn('id', $idExist)->delete();

        return true;
    }
}
