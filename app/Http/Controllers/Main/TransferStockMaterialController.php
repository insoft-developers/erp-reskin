<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\TransferStockMaterial;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferStockMaterialController extends Controller
{
    public function index()
    {
        $view = 'transfer_stock_material';

        return view('main.trasfer_stock.material.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('material_from_id', function ($data) {
                return $data->materialFrom()->first()->material_name ?? '-'.' ('.($data->materialFrom()->first()->unit ?? '-').')';
            })
            ->addColumn('stock_from', function ($data) {
                return $data->stock_from;
            })
            ->addColumn('material_to_id', function ($data) {
                return $data->materialTo()->first()->material_name ?? '-'.' ('.($data->materialTo()->first()->unit ?? '-').')';
            })
            ->addColumn('stock_to', function ($data) {
                return $data->stock_to;
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->date)->format('d F Y');
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d F Y');
            })
            ->addColumn('action', function ($data) {
                return '<div class="d-flex"><a title="Hapus Data" style="margin-right:3px;" href="javascript:void(0);" onclick="deleteData(event, ' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></div>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id', 
            'user_id',
            'material_from_id',
            'stock_from',
            'material_to_id',
            'stock_to',
            'date',
            'created_at',
        ];
        $keyword = $request->keyword;

        $data = TransferStockMaterial::orderBy('id', 'desc')
            ->where('user_id', session('id'))
            ->select($columns)
            // ->with([
            //     'md_product' => function ($query) use ($keyword) {
            //         if ($keyword != '') {
            //             $query->whereHas('material_from_id', function ($q) use ($keyword) {
            //                 $q->where('name', 'LIKE', '%' . $keyword . '%');
            //             });

            //             $query->whereHas('product_to_id', function ($q) use ($keyword) {
            //                 $q->where('name', 'LIKE', '%' . $keyword . '%');
            //             });
            //         }
            //     },
            // ])
            ->get();

        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $view = 'transfer_stock_material-create';

        return view('main.trasfer_stock.material.create', compact('view'));
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

                $tanggal_transaksi = $data['date'].' '.date('H:i:s');


                $user_id = session('id');
                $data['user_id'] = $user_id;

                $TransferStockMaterial = TransferStockMaterial::create($data);

                // STOCK KELUAR
                $material_from_id = Material::find($data['material_from_id']);
                $material_from_id['stock'] = $material_from_id['stock'] - $data['stock_from'];

                $this->logStock('md_material', $data['material_from_id'], 0, $data['stock_from'], $tanggal_transaksi);

                // STOCK MASUK
                $material_to_id = Material::find($data['material_to_id']);
                
                $cost_from = $material_from_id['cost'];
                $qtt_from = $data['stock_from'];
                $cost_from_qtt_from = $cost_from*$qtt_from;
                $qtt_origin_to = $material_to_id['stock'];
                $cost_origin_to = $material_to_id['cost'];
                $qtt_to = $data['stock_to'];
                $shadow_cost_to = $cost_from_qtt_from/$qtt_to;
                $qtt_result = $qtt_origin_to + $data['stock_to'];
                $cost_result = (($cost_origin_to*$qtt_origin_to)+($qtt_to*$shadow_cost_to))/$qtt_result;

                $material_to_id['cost'] = $cost_result;
                $material_to_id['stock'] = $material_to_id['stock'] + $data['stock_to'];
                $material_to_id->save();
                $material_from_id->save();

                $this->logStock('md_material', $data['material_to_id'], $data['stock_to'], 0, $tanggal_transaksi);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
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
        $data = TransferStockMaterial::findOrFail($id);
        $view = 'transfer_stock_material-edit';

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
                $update = TransferStockMaterial::findOrFail($id)->update($data);

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
                $delete = TransferStockMaterial::find($id);
                $this->reverseQuantity($id);

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

    public function reverseQuantity($id)
    {
        $data = TransferStockMaterial::find($id);

        // STOCK KELUAR
        $material_from_id = Material::find($data['material_from_id']);
        $material_from_id['stock'] = $material_from_id['stock'] + $data['stock_from'];
        $material_from_id->save();

        $ls = LogStock::where('relation_id', $material_from_id->id)->where('table_relation', 'md_material')->where('user_id', session('id'))->where('stock_out', $data['stock_from'])->orderBy('id', 'desc')->first();
        if($ls) {
            $ls->delete();
        }

        // STOCK MASUK
        $material_to_id = Material::find($data['material_to_id']);
        $material_to_id['stock'] = $material_to_id['stock'] - $data['stock_to'];
        $material_to_id->save();

        $ls = LogStock::where('relation_id', $material_to_id->id)->where('table_relation', 'md_material')->where('user_id', session('id'))->where('stock_in', $data['stock_to'])->orderBy('id', 'desc')->first();
        if($ls) {
            $ls->delete();
        }

        return true;
    }

    public function logStock($table, $id, $stock_in, $stock_out, $tanggal)
    {
        try {
            LogStock::create([
                'user_id' => session('id'),
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
}
