<?php

namespace App\Http\Controllers;

use App\Exports\OpnameExport;
use App\Imports\OpnameImport;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MdProduct;
use App\Models\MlCostGoodSold;
use App\Models\MlCurrentAsset;
use App\Models\OpnameItem;
use App\Models\OpnameTrash;
use App\Models\Product;
use App\Models\StockOpname;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class OpnameController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $view = 'opname';
        return view('main.manage-adjustment.opname.index', compact('view'));
    }

    public function opname_table()
    {
        $data = StockOpname::where('userid', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addColumn('created_at', function ($data) {
                return date('d-m-Y', strtotime($data->created_at));
            })
            ->addColumn('quantity', function ($data) {
                return number_format($data->quantity);
            })
            ->addColumn('total_value', function ($data) {
                return number_format($data->total_value);
            })
            ->addColumn('physical_quantity', function ($data) {
                return number_format($data->physical_quantity);
            })
            ->addColumn('physical_total_value', function ($data) {
                return number_format($data->physical_total_value);
            })
            ->addColumn('selisih_quantity', function ($data) {
                return number_format($data->selisih_quantity);
            })
            ->addColumn('selisih_total_value', function ($data) {
                return number_format($data->selisih_total_value);
            })
            ->addColumn('selisih_total_value', function ($data) {
                return number_format($data->selisih_total_value);
            })
            ->addColumn('total_adjust_value', function ($data) {
                return number_format($data->total_adjust_value);
            })
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

            ->addColumn('action', function ($data) {
                if ($data->sync_status == 1) {
                    $btn = '';
                    $btn .= '<div class="d-flex">';
                    if ($data->is_download == 1) {
                        $btn .= '<a title="Unsync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="unsync(' . $data->id . ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-scissors"></i></a>';
                    }

                    $btn .= ' <a title="Unsync Journal" style="margin-right:3px;" href="javascript:void(0);" onclick="listData(' . $data->id . ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-list"></i></a>';
                    $btn .= '<a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a>';
                    $btn .= '</div>';

                    return $btn;
                } else {
                    $btn = '';
                    $btn .= '<div class="d-flex">';
                    if ($data->is_download == 1) {
                        $btn .= '<a title="Sync Jurnal" style="margin-right:3px;" href="javascript:void(0);" onclick="syncData(' . $data->id . ')" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-sync"></i></a>';
                    }

                    $btn .= '<a title="List Produk" style="margin-right:3px;" href="javascript:void(0);" onclick="listData(' . $data->id . ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-list"></i></a>';
                    $btn .= '<a title="Upload Stock Opname" style="margin-right:3px;" href="javascript:void(0);" onclick="uploadData(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-upload"></i></a>';
                    $btn .= '<a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a>';
                    $btn .= '</div>';

                    return $btn;
                }
            })
            ->rawColumns(['action', 'sync_status', 'created_at'])
            ->make(true);
    }

    public function opname_store(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();

        try {
            $userid = $this->user_id_manage(session('id'));

            $total_quantity = 0;
            $total_value = 0;

            $so = StockOpname::create([
                'userid' => $userid,
                'description' => $input['description'],
                'quantity' => 0,
                'total_value' => 0,
                'is_download' => 0,
            ]);

            $so_id = $so->id;

            $jadi = Product::where('user_id', $userid)->where('buffered_stock', 1)->where('is_manufactured', 1)->get();
            foreach ($jadi as $j) {
                $total_quantity = $total_quantity + $j->quantity;
                $total_value = $total_value + $j->quantity * $j->cost;

                $data_insert = [
                    'opname_id' => $so_id,
                    'userid' => $userid,
                    'product_id' => $j->id,
                    'product_type' => 1,
                    'quantity' => $j->quantity,
                    'cost' => $j->cost,
                    'total_value' => $j->quantity * $j->cost,
                    'physical_quantity' => 0,
                    'physical_total_value' => 0,
                    'selisih' => $j->quantity,
                    'total_value_after_adjust' => 0,
                ];

                OpnameItem::create($data_insert);
            }

            $manufaktur = Product::where('user_id', $userid)->where('buffered_stock', 1)->where('is_manufactured', 2)->get();
            foreach ($manufaktur as $m) {
                if ($m->created_by == 1) {
                } else {
                    $total_quantity = $total_quantity + $m->quantity;
                    $total_value = $total_value + $m->quantity * $m->cost;

                    $data_insert = [
                        'opname_id' => $so_id,
                        'userid' => $userid,
                        'product_id' => $m->id,
                        'product_type' => 2,
                        'quantity' => $m->quantity,
                        'cost' => $m->cost,
                        'total_value' => $m->quantity * $m->cost,
                        'physical_quantity' => 0,
                        'physical_total_value' => 0,
                        'selisih' => $m->quantity,
                        'total_value_after_adjust' => 0,
                    ];

                    OpnameItem::create($data_insert);
                }
            }

            $material = Material::where('userid', $userid)->get();
            foreach ($material as $mat) {
                $total_quantity = $total_quantity + $mat->stock;
                $total_value = $total_value + $mat->stock * $mat->cost;
                $data_insert = [
                    'opname_id' => $so_id,
                    'userid' => $userid,
                    'product_id' => $mat->id,
                    'product_type' => 3,
                    'quantity' => $mat->stock,
                    'cost' => $mat->cost,
                    'total_value' => $mat->stock * $mat->cost,
                    'physical_quantity' => 0,
                    'physical_total_value' => 0,
                    'selisih' => $mat->stock,
                    'total_value_after_adjust' => 0,
                ];

                OpnameItem::create($data_insert);
            }

            $inter = InterProduct::where('userid', $userid)->get();
            foreach ($inter as $i) {
                $total_quantity = $total_quantity + $i->stock;
                $total_value = $total_value + $i->stock * $i->cost;
                $data_insert = [
                    'opname_id' => $so_id,
                    'userid' => $userid,
                    'product_id' => $i->id,
                    'product_type' => 4,
                    'quantity' => $i->stock,
                    'cost' => $i->cost,
                    'total_value' => $i->stock * $i->cost,
                    'physical_quantity' => 0,
                    'physical_total_value' => 0,
                    'selisih' => $i->stock,
                    'total_value_after_adjust' => 0,
                ];

                OpnameItem::create($data_insert);
            }

            StockOpname::where('id', $so_id)->update([
                'quantity' => $total_quantity,
                'total_value' => $total_value,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function opname_product_detail($id)
    {
        $master = StockOpname::findorFail($id);

        $query = OpnameItem::where('opname_id', $id);
        $data = $query->get();

        $rows = [];
        foreach ($data as $key) {
            $row['id'] = $key->id;
            $row['opname_id'] = $key->opname_id;
            $row['product_id'] = $key->product_id;

            if ($key->product_type == 1 || $key->product_type == 2) {
                $row['product_name'] = $key->product->name;
            } elseif ($key->product_type == 3) {
                $row['product_name'] = $key->material->material_name;
            } elseif ($key->product_type == 4) {
                $row['product_name'] = $key->inter->product_name;
            }

            $row['product_type'] = $key->product_type;
            $row['quantity'] = $key->quantity;
            $row['cost'] = $key->cost;
            $row['total_value'] = $key->total_value;
            $row['physical_quantity'] = $key->physical_quantity == null ? 0 : $key->physical_quantity;
            $row['physical_total_value'] = $key->physical_total_value == null ? 0 : $key->physical_total_value;
            $row['selisih'] = $key->selisih == null ? 0 : $key->selisih;
            $row['adjust_quantity'] = $key->adjust_quantity == null ? 0 : $key->adjust_quantity;
            $row['adjust_mode'] = $key->adjust_mode == null ? '' : $key->adjust_mode;
            $row['quantity_after_adjust'] = $key->quantity_after_adjust == null ? 0 : $key->quantity_after_adjust;
            $row['total_value_after_adjust'] = $key->total_value_after_adjust == null ? 0 : $key->total_value_after_adjust;

            array_push($rows, $row);
        }

        return response()->json([
            'data' => $rows,
            'master' => $master,
        ]);
    }

    public function download_template_opname($id)
    {
        $data = OpnameItem::where('opname_id', $id)->get();
        return Excel::download(new OpnameExport($data), 'stock_opname_upload_template.xlsx');
    }

    public function opname_upload(Request $request)
    {
        try {
            $excel = new OpnameImport();
            Excel::import($excel, $request->file);

            $total = $excel->get_total();

            StockOpname::where('id', $request->id)->update([
                'physical_quantity' => $total['total_fisik'],
                'physical_total_value' => $total['total_nilai_fisik'],
                'selisih_quantity' => $total['total_selisih'],
                'selisih_total_value' => $total['total_nilai_selisih'],
            ]);

            OpnameTrash::where('status', 1)->delete();

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

    public function sesuaikan_opname(Request $request)
    {
        $input = $request->all();
        $userid = $this->user_id_manage(session('id'));

        try {
            DB::beginTransaction();

            $data = OpnameItem::where('opname_id', $input['id'])->get();
            $total_quantity_adjust = 0;
            $total_nilai_adjust = 0;

            foreach ($data as $d) {
                $fisik = $d->physical_quantity == null ? 0 : $d->physical_quantity;
                $quantity = $d->quantity == null ? 0 : $d->quantity;
                $cost = $d->cost == null ? 0 : $d->cost;
                $selisih = $fisik - $quantity;

                $relasi = '';
                $mode = '';

                if ($d->product_type == 1 || $d->product_type == 2) {
                    MdProduct::where('id', $d->product_id)->update([
                        'quantity' => $d->physical_quantity,
                    ]);

                    $relasi = 'md_product';
                } elseif ($d->product_type == 3) {
                    Material::where('id', $d->product_id)->update([
                        'stock' => $d->physical_quantity,
                    ]);

                    $relasi = 'md_material';
                } elseif ($d->product_type == 4) {
                    InterProduct::where('id', $d->product_id)->update([
                        'stock' => $d->physical_quantity,
                    ]);
                    $relasi = 'md_inter_product';
                }

                if ($selisih > 0) {
                    LogStock::create([
                        'user_id' => $userid,
                        'relation_id' => $d->product_id,
                        'table_relation' => $relasi,
                        'stock_in' => abs($selisih),
                        'stock_out' => 0,
                        'relasi_trx' => 'opname_' . $input['id'],
                    ]);

                    $mode = 'addition';
                } else {
                    LogStock::create([
                        'user_id' => $userid,
                        'relation_id' => $d->product_id,
                        'table_relation' => $relasi,
                        'stock_in' => 0,
                        'stock_out' => abs($selisih),
                        'relasi_trx' => 'opname_' . $input['id'],
                    ]);

                    $mode = 'substraction';
                }

                OpnameItem::where('id', $d->id)->update([
                    'adjust_quantity' => $selisih,
                    'adjust_mode' => $mode,
                    'quantity_after_adjust' => $fisik,
                    'total_value_after_adjust' => $selisih * $cost,
                ]);

                $total_quantity_adjust = $total_quantity_adjust + $selisih;
                $total_nilai_adjust = $total_nilai_adjust + $selisih * $cost;
            }

            StockOpname::where('id', $input['id'])->update([
                'total_adjust_quantity' => $total_quantity_adjust,
                'total_adjust_value' => $total_nilai_adjust,
                'is_download' => 1,
            ]);

            $this->kirim_ke_journal($input['id']);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function opname_sync(Request $request)
    {
        $input = $request->all();
        try {
            DB::beginTransaction();
            $this->kirim_ke_journal($input['id']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function kirim_ke_journal($id)
    {
        $master = StockOpname::findorFail($id);
        $userid = $this->user_id_manage(session('id'));

        $akun_barang_jadi = MlCurrentAsset::where('userid', $userid)->where('code', 'persediaan-barang-dagang')->first();
        $akun_material = MlCurrentAsset::where('userid', $userid)->where('code', 'persediaan-bahan-baku')->first();
        $akun_setengah_jadi = MlCurrentAsset::where('userid', $userid)->where('code', 'persedian-barang-setengah-jadi')->first();
        $akun_hpp = MlCostGoodSold::where('userid', $userid)->where('code', 'harga-pokok-penjualan')->first();

        $total_barang_jadi = 0;
        $total_barang_setengah_jadi = 0;
        $total_material = 0;

        $data = OpnameItem::where('opname_id', $id)->get();

        foreach ($data as $d) {
            $cost = $d->cost == null ? 0 : $d->cost;
            $fisik = $d->physical_quantity == null ? 0 : $d->physical_quantity;
            $quantity = $d->quantity == null ? 0 : $d->quantity;
            $selisih = $fisik - $quantity;

            if ($d->product_type == 1 || $d->product_type == 2) {
                $total_barang_jadi = $total_barang_jadi + $selisih * $cost;
            } elseif ($d->product_type == 3) {
                $total_material = $total_material + $selisih * $cost;
            } elseif ($d->product_type == 4) {
                $total_barang_setengah_jadi = $total_barang_setengah_jadi + $selisih * $cost;
            }
        }

        $total_akun = abs($total_barang_jadi) + abs($total_barang_setengah_jadi) + abs($total_material);
        $waktu = strtotime(date('Y-m-d', strtotime($master->created_at)));

        $data_journal = [
            'userid' => $userid,
            'journal_id' => 0,
            'transaction_id' => 10,
            'transaction_name' => 'STOK OPNAME PENYESUIAN STOK (' . $master->description . ')',
            'rf_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
            'st_accode_id' => $akun_barang_jadi->id . '_' . $akun_barang_jadi->account_code_id,
            'nominal' => $total_akun,
            'total_balance' => $total_akun,
            'color_date' => $this->set_color(10),
            'created' => $waktu,
            'relasi_trx' => 'opname_' . $id,
        ];

        $journal_id = Journal::insertGetId($data_journal);

        if ($total_barang_jadi > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $akun_barang_jadi->id . '_' . $akun_barang_jadi->account_code_id,
                'account_code_id' => $akun_barang_jadi->account_code_id,
                'asset_data_id' => $akun_barang_jadi->id,
                'asset_data_name' => $akun_barang_jadi->name,
                'credit' => 0,
                'debet' => abs($total_barang_jadi),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => abs($total_barang_jadi),
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
                'st_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => 0,
                'debet' => abs($total_barang_jadi),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_barang_jadi->id . '_' . $akun_barang_jadi->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_barang_jadi->account_code_id,
                'asset_data_id' => $akun_barang_jadi->id,
                'asset_data_name' => $akun_barang_jadi->name,
                'credit' => abs($total_barang_jadi),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        if ($total_material > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $akun_material->id . '_' . $akun_material->account_code_id,
                'account_code_id' => $akun_material->account_code_id,
                'asset_data_id' => $akun_material->id,
                'asset_data_name' => $akun_material->name,
                'credit' => 0,
                'debet' => abs($total_material),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => abs($total_material),
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
                'st_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => 0,
                'debet' => abs($total_material),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_material->id . '_' . $akun_material->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_material->account_code_id,
                'asset_data_id' => $akun_material->id,
                'asset_data_name' => $akun_material->name,
                'credit' => abs($total_material),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        if ($total_barang_setengah_jadi > 0) {
            $data_list_insert = [
                'journal_id' => $journal_id,
                'rf_accode_id' => '',
                'st_accode_id' => $akun_setengah_jadi->id . '_' . $akun_setengah_jadi->account_code_id,
                'account_code_id' => $akun_setengah_jadi->account_code_id,
                'asset_data_id' => $akun_setengah_jadi->id,
                'asset_data_name' => $akun_setengah_jadi->name,
                'credit' => 0,
                'debet' => abs($total_barang_setengah_jadi),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => abs($total_barang_setengah_jadi),
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
                'st_accode_id' => $akun_hpp->id . '_' . $akun_hpp->account_code_id,
                'account_code_id' => $akun_hpp->account_code_id,
                'asset_data_id' => $akun_hpp->id,
                'asset_data_name' => $akun_hpp->name,
                'credit' => 0,
                'debet' => abs($total_barang_setengah_jadi),
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert);

            $data_list_insert2 = [
                'journal_id' => $journal_id,
                'rf_accode_id' => $akun_setengah_jadi->id . '_' . $akun_setengah_jadi->account_code_id,
                'st_accode_id' => '',
                'account_code_id' => $akun_setengah_jadi->account_code_id,
                'asset_data_id' => $akun_setengah_jadi->id,
                'asset_data_name' => $akun_setengah_jadi->name,
                'credit' => abs($total_barang_setengah_jadi),
                'debet' => 0,
                'is_debt' => 0,
                'is_receivables' => 0,
                'created' => $waktu,
            ];

            JournalList::insert($data_list_insert2);
        }

        $me = StockOpname::findorFail($id);
        $me->sync_status = 1;
        $me->save();
    }

    public function opname_unsync(Request $request)
    {
        $input = $request->all();
        $code = 'opname_' . $input['id'];

        $journal = Journal::where('relasi_trx', $code)->first();
        if ($journal) {
            JournalList::where('journal_id', $journal->id)->delete();
            $journal->delete();

            StockOpname::where('id', $input['id'])->update([
                'sync_status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unsync journal success!',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Unsync journal failed!',
            ]);
        }
    }

    public function opname_delete(Request $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();
            $master = StockOpname::where('id', $input['id']);
            if ($master->first()->sync_status == 1) {
                $jurnal = Journal::where('relasi_trx', 'opname_' . $input['id']);
                if ($jurnal->count() > 0) {
                    foreach ($jurnal->get() as $j) {
                        JournalList::where('id', $j->id)->delete();
                    }
                    $jurnal->delete();
                }
            }

            if ($master->first()->is_download == 1) {
                $log = LogStock::where('relasi_trx', 'opname_' . $input['id']);
                if ($log->count() > 0) {
                    foreach ($log->get() as $l) {
                        if ($l->table_relation == 'md_product') {
                            $produk = Product::findorFail($l->relation_id);
                            $stok_awal = $produk->quantity;

                            $produk->quantity = $stok_awal - $l->stock_in + $l->stock_out;
                            $produk->save();
                        } elseif ($l->table_relation == 'md_material') {
                            $produk = Material::findorFail($l->relation_id);
                            $stok_awal = $produk->stock;

                            $produk->stock = $stok_awal - $l->stock_in + $l->stock_out;
                            $produk->save();
                        } elseif ($l->table_relation == 'md_inter_product') {
                            $produk = InterProduct::findorFail($l->relation_id);
                            $stok_awal = $produk->stock;

                            $produk->stock = $stok_awal - $l->stock_in + $l->stock_out;
                            $produk->save();
                        }
                    }
                    $log->delete();
                }
            }

            OpnameItem::where('opname_id')->delete();
            $master->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
