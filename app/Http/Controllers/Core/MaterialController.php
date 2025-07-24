<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\InterComposeProduct;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialPurchaseItem;
use App\Models\ProductComposition;
use App\Models\Supplier;
use App\Models\Unit;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    
    use JournalTrait;
    public function materialList(Request $request) {
        $input = $request->all();
    	$query = Material::where('userid', $this->user_id_staff($input['userid']));
        if( ! empty($input['kata_cari'])) {
            $query->where('material_name', 'LIKE', "%".$input['kata_cari']."%"); 
        }

        $query->orderBy('id','desc');
        $data = $query->get();

       
        foreach($data as $key) {
            $row['category_name'] = $key->material_category->category_name ?? null;
            $row['supplier_name'] = $key->supplier->name ?? null;
        }

        return response()->json([
            "success" => true,
            "data" => $data,
        ]);
    }

    public function materialUnit(Request $request) {
        $data = Unit::all();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function materialCategory(Request $request) {
        $input = $request->all();
        $data = MaterialCategory::all();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }


    public function supplier(Request $request) {
        $input = $request->all();
        $data = Supplier::where('userid', $input['userid'])->get();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'material_name' => 'required',
            'unit' => 'required',
            'supplier_id' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($input, $rules);
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
                $html .=$nomor.'. '.str_replace(':', '', $o) . "\n";
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $input['userid'] = $this->user_id_staff($input['userid']);
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


    public function update(Request $request)
    {
        $input = $request->all();
        $rules = [
            'material_name' => 'required',
            'unit' => 'required',
            'supplier_id' => 'required',
            'category_id' => 'required',
        ];

        $validator = Validator::make($input, $rules);
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
                $html .=$nomor.'. '.str_replace(':', '', $o) . "\n";
            }


            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        $input['userid'] = $this->user_id_staff($input['userid']);
        $input['min_stock'] = empty($request->min_stock) ? 0 : $input['min_stock'];
        $input['ideal_stock'] = empty($request->ideal_stock) ? 0 : $input['ideal_stock'];
        $res = Material::findorFail($input['id']);
        $res->update($input);
        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }

    public function destroy(Request $request)
    {
        $input = $request->all();   
        $check = InterComposeProduct::where('material_id', $input['id'])->where('product_type', 1);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Bahan ini sudah digunakan dalam komposisi barang setengah jadi"
            ]);
        }

        $check = ProductComposition::where('material_id', $input['id'])->where('product_type', 1);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Bahan ini sudah digunakan dalam komposisi barang jadi"
            ]);
        }


        $check = MaterialPurchaseItem::where('product_id', $input['id']);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Bahan ini sudah digunakan untuk transaksi pembelian material"
            ]);
        }


        Material::destroy($input['id']);

        return response()->json([
            "success" => true,
            "message" => "success"
        ]);
    }
}
