<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\InterCategory;
use App\Models\InterComposeProduct;
use App\Models\InterProduct;
use App\Models\InterPurchase;
use App\Models\Material;
use App\Models\ProductComposition;
use App\Models\Unit;
use App\Traits\JournalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InterProductController extends Controller
{
    use JournalTrait;

    public function list(Request $request)
    {
        $input = $request->all();
        $query = InterProduct::where('userid', $this->user_id_staff($input['userid']));
        if (!empty($input['kata_cari'])) {
            $query->where('product_name', 'LIKE', '%' . $input['kata_cari'] . '%');
        }

        $query->orderBy('id', 'desc');
        $data = $query->get();

        $rows = [];
        foreach ($data as $index => $d) {
            $row['id'] = $d->id;
            $row['userid'] = $d->userid;
            $row['product_name'] = $d->product_name;
            $row['sku'] = $d->sku;
            $row['category_id'] = $d->category_id;
            $row['category_name'] = $d->inter_category->inter_category;
            $row['cost'] = $d->cost;
            $row['description'] = $d->description;
            $row['stock'] = $d->stock;
            $row['unit'] = $d->unit;
            $row['min_stock'] = $d->min_stock;
            $row['ideal_stock'] = $d->ideal_stock;
            $komposisi_array = [];
            foreach ($d->inter_compose_product as $n) {
                if ($n->product_type == 1) {
                    $mm = Material::where('id', $n->material_id);
                    if ($mm->count() > 0) {
                        $material_name = $mm->first()->material_name;
                    } else {
                        $material_name = 'not found';
                    }
                } else {
                    $mm = InterProduct::where('id', $n->material_id);
                    if ($mm->count() > 0) {
                        $material_name = $mm->first()->product_name;
                    } else {
                        $material_name = 'not found';
                    }
                }

                $list['id'] = $n->id;
                $list['material_id'] = $n->material_id;
                $list['material_name'] = $material_name;
                $list['unit'] = $n->unit;
                $list['quantity'] = $n->quantity;
                $list['product_type'] = $n->product_type;
                array_push($komposisi_array, $list);
            }
            $row['komposisi'] = $komposisi_array;
            array_push($rows, $row);
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    public function category(Request $request) {
        $input = $request->all();

        $data = InterCategory::all();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }


    public function unit(Request $request) {
        $input = $request->all();
        $data = Unit::all();
        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    public function material(Request $request) {
        $input = $request->all();
        $materials = Material::where('userid', $this->user_id_staff($input['userid']))->get();
        $inters = InterProduct::where('userid', $this->user_id_staff($input['userid']))->get();

        $rows = [];

        foreach($materials as $m) {
            $row['id'] = $m->id;
            $row['userid'] = $m->userid;
            $row['material_name'] = $m->material_name;
            $row['unit'] = $m->unit;
            $row['product_type'] = 1;
            array_push($rows, $row);
        }

        foreach($inters as $i) {
            $row['id'] = $i->id;
            $row['userid'] = $i->userid;
            $row['material_name'] = $i->product_name;
            $row['unit'] = $i->unit;
            $row['product_type'] = 2;
            array_push($rows, $row);
        }

        return response()->json([
            "success" => true,
            "data" => $rows
        ]);

    }

    public function store(Request $request)
    {
        $input = $request->all();
        $komposisi = $input['composition'];


        $rules = [
            'product_name' => 'required',
            'sku' => 'required|unique:md_inter_products',
            'unit' => 'required',
            'category_id' => 'required',
            'composition.*' => 'required',
            'quantity.*' => 'required',
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
        $input['composition'] = 0;
        $ids = InterProduct::create($input)->id;

        for ($i = 0; $i < count($komposisi); $i++) {
            $c_value = explode('_', $komposisi[$i]);
            if ($c_value[1] == 1) {
                $mat = Material::findorFail($c_value[0]);
                $satuan = $mat->unit;
            } else {
                $inter = InterProduct::findorFail($c_value[0]);
                $satuan = $inter->unit;
            }

            $data = [
                'material_id' => $c_value[0],
                'inter_product_id' => $ids,
                'unit' => $satuan,
                'quantity' => $input['quantity'][$i],
                'product_type' => $c_value[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            InterComposeProduct::insert($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }



    public function update(Request $request)
    {
        $input = $request->all();
        $komposisi = $input['composition'];

        $rules = [
            'product_name' => 'required',
            'sku' => 'required|' . Rule::unique('md_inter_products')->ignore($input['id']),
            'unit' => 'required',
            'category_id' => 'required',
            'composition.*' => 'required',
            'quantity.*' => 'required',
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

        $input['userid'] = $this->user_id_staff($input['userid']);
        // $input['stock'] = 0;
        // $input['cost'] = 0;
        $input['min_stock'] = empty($request->min_stock) ? 0 : $input['min_stock'];
        $input['ideal_stock'] = empty($request->ideal_stock) ? 0 : $input['ideal_stock'];
        $input['composition'] = 0;

        $update = InterProduct::findorFail($input['id']);
        $update->update($input);

        InterComposeProduct::where('inter_product_id', $input['id'])->delete();

        for ($i = 0; $i < count($komposisi); $i++) {
            $c_value = explode('_', $komposisi[$i]);
            if ($c_value[1] == 1) {
                $mat = Material::findorFail($c_value[0]);
                $satuan = $mat->unit;
            } else {
                $inter = InterProduct::findorFail($c_value[0]);
                $satuan = $inter->unit;
            }

            $data = [
                'material_id' => $c_value[0],
                'inter_product_id' => $input['id'],
                'unit' => $satuan,
                'quantity' => $input['quantity'][$i],
                'product_type' => $c_value[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            InterComposeProduct::insert($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }


    public function destroy(Request $request)
    {
        $input = $request->all();
        $check = InterComposeProduct::where('material_id', $input['id'])->where('product_type', 2);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam komposisi barang setengah jadi"
            ]);
        }

        $check = ProductComposition::where('material_id', $input['id'])->where('product_type', 2);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam komposisi barang jadi"
            ]);
        }

        $check = InterPurchase::where('product_id', $input['id']);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam pembuatan barang setengah jadi"
            ]);
        }

        InterComposeProduct::where('inter_product_id', $input['id'])->delete();
        InterProduct::destroy($input['id']);

        return response()->json([
            "success" => true,
            "message" => "success"
        ]);
    }

}
