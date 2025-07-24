<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\LogStock;
use App\Models\MdProduct;
use App\Models\TransferStockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferStockProductController extends Controller
{
    public function index()
    {
        $view = 'transfer_stock_product';

        return view('main.trasfer_stock.product.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('product_from_id', function ($data) {
                return $data->productFrom()->first()->name ?? '-'.' ('.($data->productFrom()->first()->unit ?? '-').')';
            })
            ->addColumn('stock_from', function ($data) {
                return $data->stock_from;
            })
            ->addColumn('product_to_id', function ($data) {
                return $data->productTo()->first()->name ?? '-'.' ('.($data->productTo()->first()->unit ?? '-').')';
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
            'product_from_id',
            'stock_from',
            'product_to_id',
            'stock_to',
            'date',
            'created_at',
        ];
        $keyword = $request->keyword;

        $data = TransferStockProduct::orderBy('id', 'desc')
            ->where('user_id', session('id'))
            ->select($columns)
            // ->with([
            //     'md_product' => function ($query) use ($keyword) {
            //         if ($keyword != '') {
            //             $query->whereHas('product_from_id', function ($q) use ($keyword) {
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
        $view = 'transfer_stock_product-create';

        return view('main.trasfer_stock.product.create', compact('view'));
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

                $TransferStockProduct = TransferStockProduct::create($data);

                // STOCK KELUAR
                $product_from_id = MdProduct::find($data['product_from_id']);
                $product_from_id['quantity'] = $product_from_id['quantity'] - $data['stock_from'];

                $this->logStock('md_product', $data['product_from_id'], 0, $data['stock_from'], $tanggal_transaksi);

                // STOCK MASUK
                $product_to_id = MdProduct::find($data['product_to_id']);

                $cost_from = $product_from_id['cost'];
                $qtt_from = $data['stock_from'];
                $cost_from_qtt_from = $cost_from*$qtt_from;
                $qtt_origin_to = $product_to_id['quantity'];
                $cost_origin_to = $product_to_id['cost'];
                $qtt_to = $data['stock_to'];
                $shadow_cost_to = $cost_from_qtt_from/$qtt_to;
                $qtt_result = $qtt_origin_to + $data['stock_to'];
                $cost_result = (($cost_origin_to*$qtt_origin_to)+($qtt_to*$shadow_cost_to))/$qtt_result;

                // dd('cost_from = '. $cost_from,
                //     'qtt_from = '. $qtt_from,
                //     'cost_from_qtt_from = '. $cost_from_qtt_from,
                //     'qtt_origin_to = '. $qtt_origin_to,
                //     'cost_origin_to = '. $cost_origin_to,
                //     'qtt_to = '. $qtt_to,
                //     'shadow_cost_to = '. $shadow_cost_to,
                //     'qtt_result = '. $qtt_result,
                //     'cost_result = '. $cost_result,
                // );

                $product_to_id['cost'] = $cost_result;
                $product_to_id['quantity'] = $product_to_id['quantity'] + $data['stock_to'];

                $product_to_id->save();
                $product_from_id->save();

                $this->logStock('md_product', $data['product_to_id'], $data['stock_to'], 0, $tanggal_transaksi);

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
        $data = TransferStockProduct::findOrFail($id);
        $view = 'transfer_stock_product-edit';

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
                $update = TransferStockProduct::findOrFail($id)->update($data);

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
                $delete = TransferStockProduct::find($id);
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
        $data = TransferStockProduct::find($id);

        // STOCK KELUAR
        $product_from_id = MdProduct::find($data['product_from_id']);
        $product_from_id['quantity'] = $product_from_id['quantity'] + $data['stock_from'];
        $product_from_id->save();

        $ls = LogStock::where('relation_id', $product_from_id->id)->where('table_relation', 'md_product')->where('user_id', session('id'))->where('stock_out', $data['stock_from'])->orderBy('id', 'desc')->first();
        
        if($ls) {
            $ls->delete();
        }
        

        // STOCK MASUK
        $product_to_id = MdProduct::find($data['product_to_id']);
        $product_to_id['quantity'] = $product_to_id['quantity'] - $data['stock_to'];
        $product_to_id->save();

        $ls = LogStock::where('relation_id', $product_to_id->id)->where('table_relation', 'md_product')->where('user_id', session('id'))->where('stock_in', $data['stock_to'])->orderBy('id', 'desc')->first();
        
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
