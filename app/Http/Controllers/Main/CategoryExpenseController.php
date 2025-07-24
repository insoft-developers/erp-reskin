<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\MdExpenseCategory;
use App\Models\MdExpenseCategoryProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryExpenseController extends Controller
{
    public function index()
    {
        $view = 'expense';

        return view('main.manage-expense.category.index', compact('view'));
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
            ->addColumn('created', function ($data) {
                return Carbon::parse($data->created)->format('d F Y');
            })
            ->addColumn('detail', function ($data) {
                $list = '<ul>';
                foreach ($data['detail'] as $key => $value) {
                    $list .= '<li>' . ($value->md_product->name ?? null) . '</li>';
                }
                $list .= '</ul>';

                return $list;
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                $btn .= '<a href="' . route('expense.category.edit', $data->id) . '" class="edit btn btn-warning btn-sm me-2">Ubah</a>';
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
            'created',
            'user_id',
        ];
        $keyword = $request->keyword;

        $data = MdExpenseCategory::orderBy('id', 'desc')
                    ->where('user_id', userOwnerId())
                    ->select($columns)
                    ->where(function($query) use ($keyword, $columns) {
                        if ($keyword != '') {
                            foreach ($columns as $column) {
                                $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                            }
                        }
                    })
                    ->get();

        foreach ($data as $key => $value) {
            $value['detail'] = $value->md_expense_category_product ?? [];
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
        $view = 'expense-create';

        return view('main.manage-expense.category.create', compact('view'));
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

                $mdExpenseCategory = MdExpenseCategory::create([
                    'name' => $data['name'],
                    'created' => now(),
                    'user_id' => $user_id,
                ]);

                if (isset($data['product_id']) && $data['product_id'] != 'all' && $data['product_id'] != null) {
                    foreach ($data['product_id'] as $key => $value) {
                        $mdExpenseCategoryProduct = MdExpenseCategoryProduct::create([
                            'expense_category_id' => $mdExpenseCategory->id,
                            'product_id' => $value,
                            'created' => now(),
                            'user_id' => $user_id,
                        ]);
                    }
                }

                return redirect()->route('expense.category.index')->with('success', 'Data Berhasil di Tambahkan!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal di Tambahkan!');
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
        $data = MdExpenseCategory::findOrFail($id);
        $view = 'category-expense-edit';

        return view('main.manage-expense.category.edit', compact('view', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->all();
        
        try {
            return $this->atomic(function () use ($data, $id) {
                $user_id = userOwnerId();

                $mdExpenseCategory = MdExpenseCategory::find($id)->update([
                    'name' => $data['name'],
                ]);

                if (isset($data['product_id']) && $data['product_id'] != 'all') {
                    foreach ($data['product_id'] as $key => $value) {
                        $mdExpenseCategoryProduct = MdExpenseCategoryProduct::create([
                            'expense_category_id' => $id,
                            'product_id' => $value,
                            'created' => now(),
                            'user_id' => $user_id,
                        ]);
                    }
                }

                return redirect()->route('expense.category.index')->with('success', 'Data Berhasil di Ubah!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal di Ubah!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $delete = MdExpenseCategory::find($id)->delete();

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
                    $delete = MdExpenseCategory::find($id)->delete();
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

    public function deleteProduct($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $delete = MdExpenseCategoryProduct::find($id)->delete();

                return redirect()->back()->with('success', 'Data Berhasil Dihapus!');
            });
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal Dihapus!');
        }
    }
}
