<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use CommonTrait;
    public function product_category_table()
    {
        $data = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addColumn('image', function ($data) {
                if (!empty($data->image)) {
                    return '<a href="' . Storage::url('images/category/' . $data->image) . '" target="_blank"><img class="img-category" src="' . Storage::url('images/category/' . $data->image) . '"></a>';
                } else {
                    return '<a href="/template/main/images/product-placeholder.png" target="_blank"><img class="img-category" src="/template/main/images/product-placeholder.png"></a>';
                }
            })
            ->addColumn('action', function ($data) {
                return '<center><a title="Edit Data" href="javascript:void(0);" onclick="editData(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-edit"></i></a><a title="Hapus Data" style="margin-top:5px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
            })
            ->rawColumns(['action', 'image'])
            ->make(true);
    }

    public function index()
    {
        $view = 'product-category';
        return view('main.product_category', compact('view'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'name' => 'required',
        ];

        if ($request->hasFile('image')) {
            $rules['image'] = 'max:500';
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

        $input['image'] = null;

        if ($request->hasFile('image')) {
            // $unik = uniqid();
            // $input['image'] = Str::slug($unik, '-').'.'.$request->image->getClientOriginalExtension();
            // $request->image->move(public_path('/template/main/images/category'), $input['image']);

            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/category', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $input['user_id'] = $this->user_id_manage(session('id'));
        ProductCategory::create($input);
        return response()->json([
            'success' => true,
            'message' => "success",
        ]);
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
        $data = ProductCategory::findorFail($id);
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $rules = [
            'name' => 'required',
        ];
        
        if ($request->hasFile('image')) {
            $rules['image'] = 'max:500';
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

        $category = ProductCategory::findorFail($id);

        $input['image'] = $category->image;
        $dir = storage_path('app/public/images/category/' . $category->image);
        if ($request->hasFile('image')) {
            // if (file_exists($dir) && !empty($category->image)) {
            //     unlink($dir);
            // }


            $extension = $request->file('image')->extension();
            $img_name = date('dmyHis') . '.' . $extension;
            $path = Storage::putFileAs('public/images/category', $request->file('image'), $img_name);
            $input['image'] = $img_name;
        }

        $input['user_id'] = $this->user_id_manage(session('id'));
        $category->update($input);
        return response()->json([
            'success' => true,
            'message' => "success",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ProductCategory::findorFail($id);
        $dir = public_path('/template/main/images/category/' . $category->image);

        // if (file_exists($dir) && !empty($category->image)) {
        //     unlink($dir);
        // }

        return  ProductCategory::destroy($id);
    }
}
