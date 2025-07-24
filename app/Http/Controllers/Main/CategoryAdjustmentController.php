<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryAdjustmentRequest;
use App\Models\CategoryAdjustment;
use Illuminate\Http\Request;

class CategoryAdjustmentController extends Controller
{
    public function index()
    {
        $view = 'category-adjustment';

        return view('main.manage-adjustment.category.index', compact('view'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('id', function ($data) {
                $checkbox = '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="' . $data->id . '" />
                </div>';

                return $checkbox;
            })
            ->addColumn('ids', function ($data) {
                return $data->id;
            })
            ->addColumn('name', function ($data) {
                return $data->name;
            })
            ->addColumn('code', function ($data) {
                return $data->code;
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
        ];
        $keyword = $request->keyword;

        $data = CategoryAdjustment::orderBy('name', 'asc')
                    ->where('account_id', userOwnerId())
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
        $view = 'category-adjustment-create';

        return view('main.manage-adjustment.category.create', compact('view'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryAdjustmentRequest $request)
    {
        $data = $request->all();

        try {
            $data['account_id'] = userOwnerId();

            return $this->atomic(function () use ($data) {
                $create = CategoryAdjustment::create($data);

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
        $data = CategoryAdjustment::findOrFail($id);
        $view = 'category-adjustment-edit';

        return view('main.manage-adjustment.category.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryAdjustmentRequest $request, string $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $update = CategoryAdjustment::findOrFail($id)->update($data);
                
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
                $delete = CategoryAdjustment::find($id)->delete();

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

    public function destroyAll(Request $request)
    {
        try {
            $ids = $request->ids;
            return $this->atomic(function () use ($ids) {
                foreach ($ids as $key => $id) {
                    $delete = CategoryAdjustment::find($id)->delete();
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
}
