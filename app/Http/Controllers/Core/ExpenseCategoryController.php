<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\MdExpenseCategory;
use App\Models\MdExpenseCategoryProduct;
use App\Models\MdProduct;
use App\Traits\CommonApiTrait;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    use JournalTrait;
    use CommonApiTrait;

    public function list(Request $request)
    {
        $query = MdExpenseCategory::with('md_expense_category_product.md_product')
            ->orderBy('id', 'desc')
            ->where('user_id', $this->user_id_staff($request->userid));
        if (!empty($request->kata_cari)) {
            $query->whereHas('md_expense_category_product.md_product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->kata_cari . '%');
            });
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $rules = array(
            "name"=> "required",
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= $nomor . '. ' . str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        try {
            return $this->atomic(function () use ($data) {
                $user_id = $this->user_id_staff($data['userid']);

                $mdExpenseCategory = MdExpenseCategory::create([
                    'name' => $data['name'],
                    'created' => now(),
                    'user_id' => $user_id,
                ]);

                if (isset($data['product_id']) && $data['product_id'] != 'all') {
                    foreach ($data['product_id'] as $key => $value) {
                        if ($value != null) {
                            $mdExpenseCategoryProduct = MdExpenseCategoryProduct::create([
                                'expense_category_id' => $mdExpenseCategory->id,
                                'product_id' => $value,
                                'created' => now(),
                                'user_id' => $user_id,
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil ditambahkan !',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function product(Request $request)
    {
        $data = MdProduct::where('user_id', $this->user_id_staff($request->userid))->get();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $rules = array(
            "name"=> "required",
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}', '"'];
            $html = '';
            $nomor = 0;
            foreach ($pesanarr as $p) {
                $nomor++;
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= $nomor . '. ' . str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $id = $data['id'];

        try {
            return $this->atomic(function () use ($data, $id) {
                $user_id = $this->user_id_staff($data['userid']);

                $mdExpenseCategory = MdExpenseCategory::find($id)->update([
                    'name' => $data['name'],
                ]);

                if (isset($data['product_id']) && $data['product_id'] != 'all') {
                    MdExpenseCategoryProduct::where('expense_category_id', $id)->delete();
                    foreach ($data['product_id'] as $key => $value) {
                        if ($value != null) {
                             $mdExpenseCategoryProduct = MdExpenseCategoryProduct::create([
                            'expense_category_id' => $id,
                            'product_id' => $value,
                            'created' => now(),
                            'user_id' => $user_id,
                        ]);
                        }
                       
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil diubah..',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => true,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        try {
            return $this->atomic(function () use ($id) {
                $delete = MdExpenseCategory::find($id)->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil Dihapus!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Data Gagal Dihapus!',
            ]);
        }
    }
}
