<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Imports\MaterialConfirmImport;
use App\Imports\MaterialImport;
use App\Models\InterComposeProduct;
use Illuminate\Http\Request;
use App\Models\Material;

use App\Models\Unit;
use App\Models\MaterialCategory;
use App\Models\MaterialPurchaseItem;
use App\Models\ProductComposition;
use App\Models\Supplier;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;


class MaterialMainController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use CommonTrait;

    public function material_table(Request $request)
    {    
        $query = Material::where('userid', $this->user_id_manage(session('id')))
            ->where('is_deleted', 0);
        if(!empty($request->keyword)) {
            $query->where('material_name', 'LIKE', '%'.$request->keyword.'%');
        }
        
        $data = $query->get();
        return DataTables::of($data)
            ->addColumn('category_id', function ($data) {
                $kat_query = MaterialCategory::where('id', $data->category_id);
                if ($kat_query->count() > 0) {
                    $kategori = $kat_query->first();
                    return $kategori->category_name;
                } else {
                    return '';
                }
            })
            ->addColumn('supplier_id', function ($data) {
                $query = Supplier::where('id', $data->supplier_id);
                if ($query->count() > 0) {
                    $real = $query->first();
                    return $real->name;
                } else {
                    return '';
                }
            })
            ->addColumn('cost', function ($data) {
                return number_format($data->cost);
            })
            ->addColumn('stock', function ($data) {
                return number_format($data->stock, 2);
            })
            ->addColumn('action', function ($data) {
                return '<center><a title="Edit Data" href="javascript:void(0);" onclick="editData(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-edit"></i></a><a title="Hapus Data" style="margin-top:5px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
            })
            ->rawColumns(['action', 'category_id', 'supplier_id', 'cost'])
            ->make(true);
    }

    public function index()
    {
        $view = 'main-material';
        $units = Unit::all();
        $categories = MaterialCategory::all();
        $suppliers = Supplier::where('userid', $this->user_id_manage(session('id')))->get();
        return view('main.material', compact('view', 'units', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'material_name' => 'required',
            'sku' => 'required',
            'unit' => 'required',
            'supplier_id' => 'required',
            'category_id' => 'required',
        ];

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

        $sku_cek = Material::where('userid', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])
            ->count();

        if ($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }

        $cek_kategori = MaterialCategory::where('id', $input['category_id']);
        if ($cek_kategori->count() > 0) {
        } else {
            $mc = new MaterialCategory();
            $mc->category_name = $input['category_id'];
            $mc->save();
            $input['category_id'] = $mc->id;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        $input['stock'] = 0;
        $input['cost'] = 0;
        $input['min_stock'] = empty($request->min_stock) ? 0 : $input['min_stock'];
        $input['ideal_stock'] = empty($request->ideal_stock) ? 0 : $input['ideal_stock'];
        Material::create($input);
        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    /*
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
        $data = Material::findorFail($id);
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $rules = [
            'material_name' => 'required',
            'sku' => 'required',
            'unit' => 'required',
            'supplier_id' => 'required',
            'category_id' => 'required',
        ];

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

        $sku_cek = Material::where('userid', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])
            ->where('id', '!=', $id)
            ->count();

        if ($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }

        $cek_kategori = MaterialCategory::where('id', $input['category_id']);
        if ($cek_kategori->count() > 0) {
        } else {
            $mc = new MaterialCategory();
            $mc->category_name = $input['category_id'];
            $mc->save();
            $input['category_id'] = $mc->id;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        // $input['stock'] = 0;
        // $input['cost'] = 0;
        $input['min_stock'] = empty($request->min_stock) ? 0 : $input['min_stock'];
        $input['ideal_stock'] = empty($request->ideal_stock) ? 0 : $input['ideal_stock'];
        $res = Material::findorFail($id);
        $res->update($input);
        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $check = InterComposeProduct::where('material_id', $id)->where('product_type', 1);
        if ($check->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed, Bahan ini sudah digunakan dalam komposisi barang setengah jadi',
            ]);
        }

        $check = ProductComposition::where('material_id', $id)->where('product_type', 1);
        if ($check->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed, Bahan ini sudah digunakan dalam komposisi barang jadi',
            ]);
        }

        $check = MaterialPurchaseItem::where('product_id', $id);
        if ($check->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed, Bahan ini sudah digunakan untuk transaksi pembelian material',
            ]);
        }

        Material::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'success',
        ]);
    }

    public function material_category_update()
    {
        $data = MaterialCategory::all();
        $html = '';
        $html .= '<option value="">Select category or Input new category</option>';
        foreach ($data as $d) {
            $html .= '<option value="' . $d->id . '">' . $d->category_name . '</option>';
        }

        return $html;
    }

    public function material_upload(Request $request)
    {
        try {
            $excel = new MaterialImport;
            Excel::import($excel, $request->file);
            $total = $excel->get_total();

            if($total > 2000) {
                return response()->json([
                    "success" => false,
                    "message" => "file upload anda terdiri dari ".$total." material sedangkan upload yang diperbolehkan untuk 1x upload adalah 2.000 (dua ribu) material"
                ]);
            } else {
                Excel::import(new MaterialConfirmImport, $request->file);
                return response()->json([
                    "success" => true,
                    "message" => "success import file"
                ]);
            }
        }catch(\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
        
       
        
    }
}
