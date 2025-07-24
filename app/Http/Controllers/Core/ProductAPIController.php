<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\InterProduct;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductComposition;
use App\Models\ProductImages;
use App\Models\ProductManufacture;
use App\Models\ProductPurchaseItem;
use App\Models\ProductVarian;
use App\Models\Unit;
use App\Traits\JournalTrait;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductAPIController extends Controller
{
    use JournalTrait;
    
    public function productList(Request $request) {
        $input = $request->all();
        $query = Product::where('user_id', $this->user_id_staff($input['userid']));
        if(! empty($input['kata_cari'])) {
            $query->where('name', 'LIKE', "%".$input['kata_cari']."%"); 
        }
        $query->orderBy('id','desc');
        if(empty($input['kata_cari'])) {
            $query->limit(25);
        }
        
        
        $data = $query->get();
        $rows = [];
        foreach($data as $key) {

            $category = ProductCategory::where('id', $key->category_id);
            if($category->count() > 0) {
                $row['category_name'] = $category->first()->name;
            } else {
                $row['category_name'] = 'not-registered';
            }

            
            $foto = ProductImages::where('product_id', $key->id)->where('main', 1);
           
            if($foto->count() > 0) {
                $path = storage_path('app/public/images/product/'.$foto->first()->url);
                if(file_exists($path)) {
                    $row['foto'] = $foto->first()->url;
                } else {
                    $row['foto'] = null;
                }   
               
            } else {
                $row['foto'] = null;
            }

            $row['id'] = $key->id;
            $row['category_id'] = $key->category_id;
            $row['code'] = $key->code;
            $row['sku'] = $key->sku;
            $row['barcode'] = $key->barcode;
            $row['name'] = $key->name;
            $row['price'] = $key->price;
            $row['cost'] = $key->cost;
            $row['default_cost'] = $key->default_cost;
            $row['unit'] = $key->unit;
            $row['quantity'] = $key->quantity;
            $row['stock_alert'] = $key->stock_alert;
            $row["sell"] = $key->sell;
            $row["created"] =  $key->created;
            $row["user_id"] = $this->user_id_staff($key->user_id);
            $row['is_variant'] = $key->is_variant;
            $row['is_manufactured'] = $key->is_manufactured;
            $row['buffered_stock'] = $key->buffered_stock;
            $row['weight'] = $key->weight;
            $row['description'] = $key->description;
            $row['created_by'] = $key->created_by;
            $row['price_ta'] = $key->price_ta;
            $row['price_mp'] = $key->price_mp;
            $row['price_cus'] = $key->price_cus;
            
            
            array_push($rows, $row);
        }

        return response()->json([
            "success" => true,
            "data" => $rows
        ]);
    }


    public function productDetail(Request $request) {
        $input = $request->all();
        $product = Product::findorFail($input['id']);

        $category = ProductCategory::where('id', $product->category_id);
        if($category->count() > 0) {
            $category_name = $category->first()->name;
        } else {
            $category_name = 'category not registered';
        }

        if($product->is_variant == 2) {
            $varian = ProductVarian::where('product_id', $input['id'])->get();
        } else {
            $varian = [];
        }

        if($product->is_manufactured == 2) {
            $kom = [];
            $koms = ProductComposition::where('product_id', $input['id'])->get();
            foreach($koms as $k) {
                if($k->product_type == 1) {
                    $material = Material::where('id', $k->material_id);
                    if($material->count() > 0) {
                        $ks['material_name'] = $material->first()->material_name;
                    } else {
                        $ks['material_name'] = 'Material not registered';
                    }
                }
                else if($k->product_type == 2) {
                    $material = InterProduct::where('id', $k->material_id);
                    if($material->count() > 0) {
                        $ks['material_name'] = $material->first()->product_name;
                    } else {
                        $ks['material_name'] = 'Material not registered';
                    }
                }
                $ks['id'] = $k->id;
                $ks['material_id'] = $k->material_id;
                $ks['product_id'] = $k->product_id;
                $ks['unit'] = $k->unit;
                $ks['quantity'] = $k->quantity;
                $ks['product_type'] = $k->product_type;
                array_push($kom, $ks);
            }
        } else {
            $kom = [];
        }

        $images = ProductImages::where('product_id', $input['id'])->get();
        $image = [];
        if($images->count() > 0) {
            foreach($images as $img) {
                $path = storage_path('app/public/images/product/'.$img->url); 
                if(file_exists($path) && ! empty($img->url))    {
                    array_push($image, $img->url);
                }      
            }
        } 

        return response()->json([
            "success" => true,
            "data" => $product,
            "varian" => $varian,
            "komposisi" => $kom,
            "category_name" => $category_name,
            "images" => $image
        ]);
    }

    public function productUnit(Request $request) {
       
        if(! empty($request->cari)) {
            $data = Unit:: where('unit_name', 'LIKE', "%".$request->cari."%")->get(); 
        } else {
            $data = Unit::all();
        }
        return response()->json([
            "success"=> true,
            "data"=> $data
        ]);
    }

    public function productComposition(Request $request) {
        $input = $request->all();
        $rows = [];
        $materials = Material::where('userid', $this->user_id_staff($input['userid']))->get();
        foreach($materials as $material) {
            $row['id'] = $material->id;
            $row['material_name'] = $material->material_name;
            $row['unit'] = $material->unit;
            $row['product_type'] = 1;
            $row['type_name'] = "Bahan baku";
            array_push($rows, $row);
        }

        $inters = InterProduct::where('userid', $this->user_id_staff($input['userid']))->get();
        foreach($inters as $inter) {
            $row['id'] = $inter->id;
            $row['material_name'] = $inter->product_name;
            $row['unit'] = $inter->unit;
            $row['product_type'] = 2;
            $row['type_name'] = "Barang setengah jadi";
            array_push($rows, $row);
        }

        return response()->json([
            "success" => true,
            "data" => $rows
        ]);
        
    }

    public function productStore(Request $request) {
        $input = $request->all();
       
        
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            'sku' => 'required',
            'price' => 'required',
            'is_variant' => 'required',
            'unit' => 'required',
            'buffered_stock' => 'required',
            'is_manufactured' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
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

        try {
            $input['cost'] = $request->is_manufactured == 2 ? 0 : $input['cost'];
            $input['default_cost'] = $request->is_manufactured == 2 ? 0 : $input['cost'];
            $input['stock_alert'] = empty($input['stock_alert']) ? 0 : $input['stock_alert'];
            $input['sell'] = 0;
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $this->user_id_staff($input['user_id']);
            $input['quantity'] = 0;
            $input['description'] = $input['description'];
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            
            $product = Product::create($input);
            $product_id = $product->id;

            return response()->json([
                'success' => true,
                'message' => 'success',
                'id' => $product_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function productVarianStore(Request $request) {
        $input = $request->all();
        
        $new = new ProductVarian();
        $new->product_id = $input['id'];
        $new->varian_group = $input['varian_group'];
        $new->varian_name = $input['varian_name'];
        $new->sku = $input['varian_sku'];
        $new->varian_price = $input['varian_price'];
        $new->single_pick = $input['single_pick'];
        $new->max_quantity = $input['max_quantity'];
        $new->created_at = now();
        $new->updated_at = now();
        $new->save();  
        return response()->json([
            'success' => true
        ]);
    }


    public function productCompositionStore(Request $request) {
        $input = $request->all();

        $m_product = Product::findorFail($input['id']);
        $cost_awal = $m_product->cost;

        $c_value = $input['product_type'];
        if ($c_value == 1) {
            $mat = Material::findorFail($input['product_id']);
            $satuan = $mat->unit;
            $cost_tambahan = $mat->cost * $request->quantity;
        } else {
            $inter = InterProduct::findorFail($input['product_id']);
            $satuan = $inter->unit;
            $cost_tambahan = $inter->cost * $request->quantity;
        }
        $com = new ProductComposition();
        $com->material_id = $input['product_id'];
        $com->product_id = $input['id'];
        $com->unit = $satuan;
        $com->quantity = $input['quantity'];
        $com->product_type = $c_value;
        $com->created_at = now();
        $com->updated_at = now();
        $com->save();

        if($m_product->created_by == 1) {
            $m_product->cost = $cost_awal + $cost_tambahan;
            $m_product->save(); 
        }
       

        return response()->json([
            'success' => true
        ]);
        

    }


    public function productImageUpload(Request $request) {
        $ids = $request->ids;
        $images = ProductImages::where('product_id', $ids)->get();
            
        if($images->count() > 0) {
            // foreach($images as $image) {
            //     $path = storage_path('app/public/images/product/'.$image->url);
            //     if(file_exists($path) && ! empty($image->url)) {
            //         unlink($path);
            //     }
            // }

            ProductImages::where('product_id', $ids)->delete();
        }

        $path = storage_path('app/public/images/product');

        try {
            if($request->has('file')) {  
                $manager = new ImageManager(new Driver());
                $files = $request->file;
                foreach($files as $index => $file) {
                    $filename = date('YmdHis').$file->getClientOriginalName();
                    $img = $manager->read($file->path());
                    $img->resize(500,500, function($constraint){
                        $constraint->aspectRatio();
                    })->save($path.'/'.$filename);

                    $data = new ProductImages;
                    $data->product_id = $ids;
                    $data->url = $filename;
                    $data->main = $index == 0 ? 1: 0;
                    $data->created = date('Y-m-d H:i:s');
                    $data->save();
                }
             }
    
             return response()->json(['result'=>'success']);

        }catch(\Exception $e) {
            return response()->json(['success'=> false, 'message'=> $e->getMessage()]);
        }
    }


    public function productUpdate(Request $request) {
        $input = $request->all();
       
        
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'is_variant' => 'required',
            'unit' => 'required',
            'buffered_stock' => 'required',
            'is_manufactured' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
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

        try {
            $product = Product::findorFail($input['id']);
            $input['cost'] = $request->is_manufactured == 2 ? $product->cost : $input['cost'];
            $input['default_cost'] = $request->is_manufactured == 2 ? $product->cost : $input['cost'];
            $input['stock_alert'] = empty($input['stock_alert']) ? 0 : $input['stock_alert'];
            $input['weight'] = empty($input['weight']) ? 0 : $input['weight'];
            $input['sell'] = 0;
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $this->user_id_staff($input['user_id']);
            $input['quantity'] = $product->quantity;
            $input['description'] = $input['description'];
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            
            $product->update($input);
            $product_id = $input['id'];

            ProductVarian::where('product_id', $product_id)->delete();
            ProductComposition::where('product_id', $product_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'success',
                'id' => $product_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function productDelete(Request $request) {
        $input = $request->all();
        $id = $input['id'];

        $cek1 = ProductPurchaseItem::where('product_id', $id)->count();
        if($cek1 > 0) {
            return response()->json([
                "success" => false,
                "message" =>  'Hapus Produk Gagal karena produk sudah terdaftar pada Pembelian Produk Jadi'
            ]);
        }

        $cek2 = ProductManufacture::where('product_id', $id)->count();
        if($cek2 > 0) {
            return response()->json([
                "success" => false,
                "message" =>  'Hapus Produk Gagal karena produk sudah terdaftar pada Proses Produk Manufaktur'
            ]);
        }


        
        try {
            DB::beginTransaction();

            $images = ProductImages::where('product_id', $id)->get();
            
            if($images->count() > 0) {
                // foreach($images as $image) {
                //     $path = storage_path('app/public/images/product/'.$image->url);
                //     if(file_exists($path) && ! empty($image->url)) {
                //         unlink($path);
                //     }
                // }
    
                ProductImages::where('product_id', $id)->delete();
            }

            DB::table('md_products')->where('id', $id)->delete();
            ProductVarian::where('product_id', $id)->delete();
            ProductComposition::where('product_id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sukses Hapus Produk',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
}

            
