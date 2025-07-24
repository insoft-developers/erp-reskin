<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use App\Models\MlAccount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $view = 'discount';

        return view('main.crm.discount.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('code', function ($data) {
                return $data->code;
            })
            ->addColumn('min_order', function ($data) {
                return 'Rp. ' . number_format($data->min_order, 0, ',', '.');
            })
            ->addColumn('value', function ($data) {
                if ($data->type == 'persen') {
                    return $data->value . '%';
                }else if ($data->type == 'nominal') {
                    return 'Rp. ' . number_format($data->value, 0, ',', '.');
                }
            })
            ->addColumn('expired_at', function ($data) {
                return date('d F Y', strtotime($data->expired_at));
            })
            ->addColumn('status', function ($data) {
                if ($data->expired_at < date('Y-m-d')) {
                    return '<span class="badge bg-danger">Expired</span>';
                }else {
                    return '<span class="badge bg-success">Active</span>';
                }
            })
            // ->addColumn('max_use', function ($data) {
            //     return $data->max_use;
            // })
            ->addColumn('allowed_multiple_use', function ($data) {
                return ($data->allowed_multiple_use == 1) ? 'Ya' : 'Tidak';
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm me-2"  onclick="editData(' . $data->id . ')">Ubah</a>';
                $btn .= '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="deleteData(event, ' . $data->id . ')">Hapus</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $columns = [
            'id',
            'name',
            'code',
            'type',
            'value',
            'expired_at',
            'min_order',
            // 'max_use',
            'account_id',
            'allowed_multiple_use',
        ];
        $keyword = $request->keyword;
        $user = MlAccount::find(session('id'));
        $allUserId = MlAccount::where('branch_id', $user->branch_id)->pluck('id')->toArray();

        $data = Discount::orderBy('expired_at', 'asc')
                    ->whereIn('account_id', $allUserId)
                    ->select($columns)
                    ->where(function($query) use ($keyword, $columns) {
                        if ($keyword != '') {
                            foreach ($columns as $column) {
                                $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                            }
                        }
                    })
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
        $view = 'discount-create';

        return view('main.crm.discount.create', compact('view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiscountRequest $request)
    {
        $data = $request->all();

        try {
            $data['account_id'] = $this->get_owner_id(session('id'));
            $data['value'] = str_replace('.', '', $data['value']);
            $data['min_order'] = str_replace('.', '', $data['min_order']);

            return $this->atomic(function () use ($data) {
                $create = Discount::create($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan!',
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
        $data = Discount::findOrFail($id);
        $view = 'followup-edit';

        return view('main.crm.discount.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DiscountRequest $request, string $id)
    {
        $data = $request->all();

        try {
            $data['value'] = str_replace('.', '', $data['value']);
            $data['min_order'] = str_replace('.', '', $data['min_order']);
    
            return $this->atomic(function () use ($data, $id) {
                $update = Discount::findOrFail($id)->update($data);
                
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
                $delete = Discount::find($id)->delete();

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
}
