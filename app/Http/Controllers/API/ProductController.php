<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\MdProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductComposition;
use App\Models\ProductImages;
use App\Models\ProductManufacture;
use App\Models\ProductPurchaseItem;
use App\Models\ProductVarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));
        
        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            // 'sku' => 'required|unique:md_products',
            'price' => 'required',
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

        try {
            if ($request->has('barcode') && $request->barcode != '') {
                $alreadyBarcode = MdProduct::where('barcode', $input['barcode'])->where('user_id', $userId)->first();
    
                if ($alreadyBarcode) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode Barcode Sudah Terdaftar',
                    ]);
                }
            }

            $input['barcode'] = $input['barcode'];
            $input['cost'] = 0;
            $input['buffered_stock'] = 0;
            $input['default_cost'] = 0;
            $input['stock_alert'] = 0;
            $input['sell'] = 0;
            $input['unit'] = 'Unit (Satuan)';
            $input['is_variant'] = 1;
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $userId;
            $input['quantity'] = 0;
            $input['is_manufactured'] = 1;
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            $product = Product::create($input);
            $product_id = $product->id;

            if ($request->hasFile('image')) {
                $count = count($_FILES['image']['name']);

                for ($i = 0; $i < $count; $i++) {
                    $unik = uniqid();
        
                    $extension = $request->file('image')[$i]->extension();
                    $img_name = $unik.date('dmyHis').'.'.$extension;
                    $path = Storage::putFileAs('public/images/product', $request->file('image')[$i], $img_name);
                    $input['image'][$i] = $img_name;



                    $im = new ProductImages();
                    $im->product_id = $product_id;
                    $im->url = $input['image'][$i];
                    $im->main = $i == 0 ? 1 : 0;
                    $im->created = now();
                    $im->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Product Created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));

        $rules = [
            'name' => 'required',
            'category_id' => 'required',
            // 'sku' => 'required|' . Rule::unique('md_products')->ignore($id),
            'price' => 'required',
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
                'status' => false,
                'message' => $html,
            ]);
        }

        try {
            $product = Product::findorFail($id);

            if ($request->has('barcode') && $request->barcode != '') {
                $alreadyBarcode = MdProduct::where('id', '!=', $product->id)->where('barcode', $input['barcode'])->where('user_id', $userId)->first();
    
                if ($alreadyBarcode) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode Barcode Sudah Terdaftar',
                    ]);
                }
            }
            
            $input['barcode'] = $input['barcode'];
            $input['cost'] = 0;
            $input['default_cost'] = 0;
            $input['stock_alert'] = 0;
            $input['sell'] = 0;
            $input['is_variant'] = 1;
            $input['is_manufactured'] = 1;
            $input['unit'] = 'Unit (Satuan)';
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $userId;
            $input['quantity'] = $product->quantity;
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            $product->update($input);

            $product_id = $id;

            if ($request->hasFile('image')) {
                $gambar = ProductImages::where('product_id', $product_id)->get();
                $dir = storage_path('app/public/images/product');
                if ($gambar->count() > 0) {
                    foreach ($gambar as $g) {
                        $x_dir = $dir . '/' . $g->url;

                        if (file_exists($x_dir) && !empty($g->url)) {
                            unlink($x_dir);
                        }
                    }
                    ProductImages::where('product_id', $product_id)->delete();
                }

                

                $count = count($_FILES['image']['name']);

                for ($i = 0; $i < $count; $i++) {
                    $unik = uniqid();
        
                    $extension = $request->file('image')[$i]->extension();
                    $img_name = $unik.date('dmyHis').'.'.$extension;
                    $path = Storage::putFileAs('public/images/product', $request->file('image')[$i], $img_name);
                    $input['image'][$i] = $img_name;

                    $im = new ProductImages();
                    $im->product_id = $product_id;
                    $im->url = $input['image'][$i];
                    $im->main = $i == 0 ? 1 : 0;
                    $im->created = now();
                    $im->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Produk Berhasil Diubah.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
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

            $cek_gambar = DB::table('md_product_images')->where('product_id', $id)->get();
            if ($cek_gambar->count() > 0) {
                foreach ($cek_gambar as $cg) {
                    $dir = storage_path('app/public/images/product/'.$cg->url);
                    if (file_exists($dir) && !empty($cg->url)) {
                        unlink($dir);
                    }
                }
                DB::table('md_product_images')->where('product_id', $id)->delete();
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

    public function storeCategory(Request $request)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data) {
                $data['user_id'] = Auth::user()->id ?? session('id');
                $create = ProductCategory::create($data);

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

    public function updateCategory(Request $request, $id)
    {
        $data = $request->all();

        try {
            return $this->atomic(function () use ($data, $id) {
                $create = ProductCategory::find($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Ubah!',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Ubah!',
            ]);
        }
    }
}
