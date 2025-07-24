<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\InterProduct;
use App\Models\InterCategory;
use App\Models\Unit;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Models\InterComposeProduct;
use App\Models\InterPurchase;
use App\Models\ProductComposition;
use App\Traits\CommonTrait;

class InterProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use CommonTrait;

    public function inter_table()
    {
        $data = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();
        return DataTables::of($data)
            ->addColumn('category_id', function ($data) {
                $query = InterCategory::where('id', $data->category_id);
                if ($query->count() > 0) {
                    return $query->first()->inter_category;
                } else {
                    return '';
                }
            })
            ->addColumn('cost', function($data){
                return number_format($data->cost);
            })
            ->addColumn('composition', function ($data) {
                $query = InterComposeProduct::where('inter_product_id', $data->id)->get();
                $c = '';
                $c .= '<ul>';
                foreach ($query as $q) {
                    if ($q->product_type == 1) {
                        $m = Material::findorFail($q->material_id);
                        $material_name = $m->material_name;
                    } else {
                        $m = InterProduct::findorFail($q->material_id);
                        $material_name = $m->product_name;
                    }
                    $c .= '<li>' . $material_name . ' - ' . $q->quantity . ' ' . $q->unit . '</li>';
                }
                $c .= '</ul>';
                return $c;
            })

            ->addColumn('action', function ($data) {
                return '<center><a title="Edit Data" href="javascript:void(0);" onclick="editData(' . $data->id . ')" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-edit"></i></a><a title="Hapus Data" style="margin-top:5px;" href="javascript:void(0);" onclick="deleteData(' . $data->id . ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
            })
            ->rawColumns(['action', 'category_id', 'composition','cost'])
            ->make(true);
    }

    public function index()
    {
        $view = 'inter-product';
        $units = Unit::all();
        $categories = InterCategory::all();
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $inters = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();
        return view('main.inter_product', compact('view', 'units', 'categories', 'materials', 'inters'));
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
        $komposisi = $input['composition'];

        $rules = [
            'product_name' => 'required',
            'sku' => 'required',
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

        $sku_cek = InterProduct::where('userid', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])->count();

        if($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }


        $cek_kategori = InterCategory::where('id', $input['category_id']);
        if ($cek_kategori->count() > 0) {
        } else {
            $mc = new InterCategory();
            $mc->inter_category = $input['category_id'];
            $mc->save();
            $input['category_id'] = $mc->id;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
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
        $inter = InterProduct::findorFail($id);
        $detail = InterComposeProduct::where('inter_product_id', $id)->get();

        $all_material = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $all_inter = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();

        $html = '';
        $com_index = 1;

        foreach ($detail as $index => $d) {
            if ($index > 0) {
                $com_index++;

                $html .= '<div class="row baris mtop10 baris-tambahan" id="baris_' . $com_index . '">';
                $html .= '<div class="col-md-8">';
                $html .= '<select class="form-control cust-control select-item" id="composition_' . $com_index . '" name="composition[]">';
                $html .= '<option value="">Pilih komposisi bahan</option>';
                $html .= '<optgroup label="Bahan Baku">';
                foreach ($all_material as $am) {
                    if ($am->id . '_' . 1 == $d->material_id . '_' . $d->product_type) {
                        $html .= '<option value="' . $am->id . '_' . 1 . '" selected>' . $am->material_name . ' - ' . $am->unit . '</option>';
                    } else {
                        $html .= '<option value="' . $am->id . '_' . 1 . '">' . $am->material_name . ' - ' . $am->unit . '</option>';
                    }
                }

                $html .= '</optgroup>';
                $html .= '<optgroup label="Barang Setengah Jadi">';
                foreach ($all_inter as $ai) {
                    if ($ai->id . '_' . 2 == $d->material_id . '_' . $d->product_type) {
                        $html .= '<option value="' . $ai->id . '_' . 2 . '" selected>' . $ai->product_name . ' - ' . $ai->unit . '</option>';
                    } else {
                        $html .= '<option value="' . $ai->id . '_' . 2 . '">' . $ai->product_name . ' - ' . $ai->unit . '</option>';
                    }
                }

                $html .= '</optgroup>';
                $html .= '</select>';
                $html .= '</div>';
                $html .= '<div class="col-md-3">';
                $html .= '<input value="' . $d->quantity . '" type="text" class="form-control cust-control" id="quantity_' . $com_index . '" name="quantity[]" placeholder="quantitiy">';
                $html .= '</div>';
                $html .= '<div class="col-md-1">';
                $html .= '<center><a onclick="delete_composition_item(' . $com_index . ')" href="javascript:void(0);" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }

        $data['data'] = $inter;
        $data['detail'] = $detail;
        $data['html'] = $html;
        $data['count'] = $detail->count();

        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $komposisi = $input['composition'];

        $rules = [
            'product_name' => 'required',
            'sku' => 'required',
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

        $sku_cek = InterProduct::where('userid', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])
            ->where('id', '!=', $id)
            ->count();

        if($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }


        $cek_kategori = InterCategory::where('id', $input['category_id']);
        if ($cek_kategori->count() > 0) {
        } else {
            $mc = new InterCategory();
            $mc->inter_category = $input['category_id'];
            $mc->save();
            $input['category_id'] = $mc->id;
        }

        $input['userid'] = $this->user_id_manage(session('id'));
        // $input['stock'] = 0;
        // $input['cost'] = 0;
        $input['min_stock'] = empty($request->min_stock) ? 0 : $input['min_stock'];
        $input['ideal_stock'] = empty($request->ideal_stock) ? 0 : $input['ideal_stock'];
        $input['composition'] = 0;

        $update = InterProduct::findorFail($id);
        $update->update($input);

        InterComposeProduct::where('inter_product_id', $id)->delete();

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
                'inter_product_id' => $id,
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $check = InterComposeProduct::where('material_id', $id)->where('product_type', 2);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam komposisi barang setengah jadi"
            ]);
        }

        $check = ProductComposition::where('material_id', $id)->where('product_type', 2);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam komposisi barang jadi"
            ]);
        }

        $check = InterPurchase::where('product_id', $id);
        if($check->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Failed, Product ini sudah digunakan dalam pembuatan barang setengah jadi"
            ]);
        }

        InterComposeProduct::where('inter_product_id', $id)->delete();
        InterProduct::destroy($id);

        return response()->json([
            "success" => true,
            "message" => "success"
        ]);
    }

    public function get_data_non_product()
    {
        $data['material'] = Material::where('userid', $this->user_id_manage($this->user_id_manage(session('id'))))->get();
        $data['inter'] = InterProduct::where('userid', $this->user_id_manage($this->user_id_manage(session('id'))))->get();
        return $data;
    }

    public function inter_category_update()
    {
        $data = InterCategory::all();
        $html = '';
        $html .= '<option value="">Select category or Input new category</option>';
        foreach ($data as $d) {
            $html .= '<option value="' . $d->id . '">' . $d->inter_category . '</option>';
        }

        return $html;
    }
}
