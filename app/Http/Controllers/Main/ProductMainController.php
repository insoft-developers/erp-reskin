<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImages;

use App\Models\ProductCategory;
use App\Exports\ProductExport;
use App\Imports\ProductConfirmImport;
use App\Imports\ProductImport;
use App\Models\InterProduct;
use App\Models\Material;
use App\Models\ProductComposition;
use App\Models\ProductManufacture;
use App\Models\ProductPurchaseItem;
use App\Models\ProductVarian;
use App\Models\Unit;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;

class ProductMainController extends Controller
{
    use CommonTrait;
    public function product_table(Request $request)
    {
        $input = $request->all();

        $query = Product::with('composition', 'composition.material', 'composition.inter')
            ->orderBy('id', 'desc')
            ->where('user_id', $this->user_id_manage(session('id')));
        if (!empty($input['category'])) {
            $query->where('category_id', $input['category']);
        }

        if (!empty($input['persamaan'])) {
            if ($input['persamaan'] == 1) {
                $query->where('quantity', '>', $input['stock']);
            } elseif ($input['persamaan'] == 2) {
                $query->where('quantity', '<', $input['stock']);
            } elseif ($input['persamaan'] == 3) {
                $query->where('quantity', $input['stock']);
            }
        }

        $data = $query->get();
        return DataTables::of($data)
            ->addColumn('komposisi', function ($data) {
                $html = '';
                if ($data->is_manufactured == 1) {
                    $html .= '<span style="color:green;font-weight:bold;">Beli Jadi</span>';
                } elseif ($data->is_manufactured == 2) {
                    if ($data->created_by == 1) {
                        $html .= '<span style="color:orange;font-weight:bold;">Manufaktur (By Pesanan)</span>';
                        $html .= '<ul>';
                        foreach ($data->composition as $dc) {
                           if ($dc->product_type == 1) {

                                $productName = isset($dc->material) && isset($dc->material->material_name) ? $dc->material->material_name : 'Tidak diketahui';
                                $quantity = $dc->quantity ?? '0';

                                $html .= "<li>$productName ( $quantity )</li>";

                            } elseif ($dc->product_type == 2) {
                                $productName = isset($dc->inter) && isset($dc->inter->product_name) ? $dc->inter->product_name : 'Tidak diketahui';
                                $quantity = $dc->quantity ?? '0';

                                $html .= "<li>$productName ( $quantity )</li>";

                                
                            }
                        }
                        $html .= '</ul>';
                    } else {
                        $html .= '<span style="color:blue;font-weight:bold;">Manufaktur (Dibuat Dahulu)</span>';
                        $html .= '<ul>';
                        foreach ($data->composition as $dc) {
                            if ($dc->product_type == 1) {

                                $productName = isset($dc->material) && isset($dc->material->material_name) ? $dc->material->material_name : 'Tidak diketahui';
                                $quantity = $dc->quantity ?? '0';

                                $html .= "<li>$productName ( $quantity )</li>";

                            } elseif ($dc->product_type == 2) {
                                $productName = isset($dc->inter) && isset($dc->inter->product_name) ? $dc->inter->product_name : 'Tidak diketahui';
                                $quantity = $dc->quantity ?? '0';

                                $html .= "<li>$productName ( $quantity )</li>";

                                
                            }
                        }
                        $html .= '</ul>';
                    }
                }

                return $html;
            })
            ->addColumn('stock_alert', function ($data) {
                return $data->stock_alert . ' ' . $data->unit;
            })
            ->addColumn('pilih', function ($data) {
                return '<input class="chechbox-id" type="checkbox" id="id" data-id="' . $data->id . '">';
            })
            ->addColumn('display', function ($data) {
                if ($data->store_displayed === 1) {
                    return '<center><input type="checkbox" onclick="change_display(' . $data->id . ', 0)" class="checkbox-display" checked ="checked" id="display_id_' . $data->id . '" data-id="' . $data->id . '"> </center>';
                } else {
                    return '<center><input type="checkbox" onclick="change_display(' . $data->id . ', 1)" class="checkbox-display" id="display_id_' . $data->id . '" data-id="' . $data->id . '"></center>';
                }
            })
            ->addColumn('editable', function ($data) {
                if ($data->is_editable === 1) {
                    return '<center><input type="checkbox" onclick="change_editable(' . $data->id . ', 0)" class="checkbox-display" checked ="checked" id="editable_id_' . $data->id . '" data-id="' . $data->id . '"> </center>';
                } else {
                    return '<center><input type="checkbox" onclick="change_editable(' . $data->id . ', 1)" class="checkbox-display" id="editable_id_' . $data->id . '" data-id="' . $data->id . '"></center>';
                }
            })
            ->addColumn('buffered_stock', function ($data) {
                if ($data->is_manufactured === 1) {
                    if ($data->buffered_stock === 1) {
                        return '<center><input type="checkbox" onclick="use_stock(' . $data->id . ', 0)" class="checkbox-display" checked ="checked" id="use_id_' . $data->id . '" data-id="' . $data->id . '"> </center>';
                    } else {
                        return '<center><input type="checkbox" onclick="use_stock(' . $data->id . ', 1)" class="checkbox-display" id="use_id_' . $data->id . '" data-id="' . $data->id . '"></center>';
                    }
                } else {
                    if ($data->created_by == 1) {
                        Product::where('id', $data->id)->update(['buffered_stock' => 0]);
                        $type = 1;
                    } else {
                        Product::where('id', $data->id)->update(['buffered_stock' => 1]);
                        $type = 0;
                    }

                    if ($data->buffered_stock === 1) {
                        return '<center><input onclick="use_banned(' . $type . ')" type="checkbox" class="checkbox-display" checked ="checked" id="use_id_' . $data->id . '"> </center>';
                    } else {
                        return '<center><input onclick="use_banned(' . $type . ')" type="checkbox" class="checkbox-display" id="use_id_' . $data->id . '"></center>';
                    }
                }
            })
            ->addColumn('product_image', function ($data) {
                $image_query = ProductImages::where('product_id', $data->id)->where('main', 1);
                if ($image_query->count() > 0) {
                    $image = $image_query->first();
                    return '<a href="' . Storage::url('images/product/' . $image->url) . '" target="_blank"><img class="product-images" src="' . Storage::url('images/product/' . $image->url) . '"></a>';
                } else {
                    return '<img class="product-images" src="' . asset('template/main/images/product-placeholder.png') . '">';
                }
            })
            ->addColumn('category', function ($data) {
                $cat = ProductCategory::where('id', $data->category_id);
                if ($cat->count() > 0) {
                    $kategori = $cat->first();
                    return $kategori->name;
                } else {
                    return '';
                }
            })
            ->addColumn('price', function ($data) {
                return 'Rp. ' . number_format($data->price);
            })
            ->addColumn('cost', function ($data) {
                return 'Rp. ' . number_format($data->cost);
            })
            ->addColumn('stock', function ($data) {
                return number_format($data->quantity);
            })
            ->addColumn('product_value', function ($data) {
                $value = $data->cost * $data->quantity;
                return 'Rp. ' . number_format($value);
            })
            ->addColumn('margin', function ($data) {
                $margin = $data->price - $data->cost;
                return number_format($margin);
            })
            ->addColumn('persen_margin', function ($data) {
            $persen_margin = $data->price == 0 ? '0%' : number_format((($data->price - $data->cost) / $data->price) * 100, 2) . '%';
            return $persen_margin;
            })

            ->addColumn('action', function ($data) {
                return '<center><button onclick="detailData(' . $data->id . ')" title="Detail Data" style="width:70px;margin-bottom:5px;" class="btn btn-info btn-sm btn-custom"><i class="fa fa-list"></i></button><a href="' . url('product/' . $data->id . '/edit') . '"><button title="Edit Data" style="width:70px;margin-bottom:5px;" class="btn btn-warning btn-sm btn-custom"><i class="fa fa-edit"></i></button></a><button title="Delete Data" onclick="product_single_delete(' . $data->id . ')" style="width:70px;" class="btn btn-danger btn-sm btn-custom"><i class="fa fa-remove"></i></button></center>';
            })
            ->rawColumns(['action', 'product_image', 'category', 'price', 'cost', 'stock', 'product_value', 'pilih', 'margin', 'persen_margin', 'display', 'editable', 'buffered_stock', 'komposisi'])
            ->make(true);
    }

    public function index()
    {
        $view = 'product-list';
        $product_category = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->get();
        return view('main.product_list', compact('view', 'product_category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = 'product-add';
        $categories = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->get();
        $units = Unit::all();

        return view('main.product_add', compact('view', 'categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        if ($request->hasFile('image')) {
            $rules['image.*'] = 'max:500';
        }

        if ($request->is_variant == 2) {
            $rules['varian_name.*'] = 'required';
            $rules['varian_price.*'] = 'required';
            $rules['varian_group.*'] = 'required';
        }

        if ($request->is_manufactured == 2) {
            $rules['composition.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric';
        } elseif ($request->is_manufactured == 1) {
            $rules['cost'] = 'required';
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

        $sku_cek = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])
            ->count();

        if ($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }

        try {
            $input['cost'] = $request->is_manufactured == 2 ? 0 : $input['cost'];
            $input['default_cost'] = $request->is_manufactured == 2 ? 0 : $input['cost'];
            $input['stock_alert'] = empty($input['stock_alert']) ? 0 : $input['stock_alert'];
            $input['sell'] = 0;
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $this->user_id_manage(session('id'));
            $input['quantity'] = 0;
            $input['description'] = $input['desc_assist'];
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            $input['store_displayed'] = 1;
            $product = Product::create($input);
            $product_id = $product->id;

            if ($request->is_variant == 2) {
                for ($i = 0; $i < count($input['varian_name']); $i++) {
                    $new = new ProductVarian();
                    $new->product_id = $product_id;
                    $new->varian_group = $input['varian_group'][$i];
                    $new->varian_name = $input['varian_name'][$i];
                    $new->sku = $input['varian_sku'][$i];
                    $new->varian_price = $input['varian_price'][$i];
                    $new->single_pick = $input['single_pick'][$i];
                    $new->max_quantity = $input['max_quantity'][$i];
                    $new->created_at = now();
                    $new->updated_at = now();
                    $new->save();
                }
            }

            if ($request->is_manufactured == 2) {
                $cogs_awal = 0;
                for ($i = 0; $i < count($input['composition']); $i++) {
                    $c_value = explode('_', $input['composition'][$i]);
                    if ($c_value[1] == 1) {
                        $mat = Material::findorFail($c_value[0]);
                        $satuan = $mat->unit;
                        $cogs_awal = $cogs_awal + $mat->cost * $request->quantity[$i];
                    } else {
                        $inter = InterProduct::findorFail($c_value[0]);
                        $satuan = $inter->unit;
                        $cogs_awal = $cogs_awal + $inter->cost * $request->quantity[$i];
                    }
                    $com = new ProductComposition();
                    $com->material_id = $c_value[0];
                    $com->product_id = $product_id;
                    $com->unit = $satuan;
                    $com->quantity = $request->quantity[$i];
                    $com->product_type = $c_value[1];
                    $com->created_at = now();
                    $com->updated_at = now();
                    $com->save();
                }

                if ($request->created_by == 1) {
                    Product::where('id', $product_id)->update(['cost' => $cogs_awal]);
                }
            }

            if ($request->hasFile('image')) {
                $count = count($_FILES['image']['name']);

                for ($i = 0; $i < $count; $i++) {
                    $unik = uniqid();

                    $extension = $request->file('image')[$i]->extension();
                    $img_name = $unik . date('dmyHis') . '.' . $extension;
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
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findorFail($id);
        $category = ProductCategory::findorFail($product->category_id);
        $foto = ProductImages::where('product_id', $id)->get();

        $html = '';
        $html .= '<table class="table table-striped">';
        if ($foto->count() > 0) {
            $html .= '<tr>';
            $html .= '<td>Foto Produk</td>';
            $html .= '<td>';
            foreach ($foto as $f) {
                $html .= '<a href="' . Storage::url('images/product/' . $f->url) . '" target="_blank"><img class="image-detail-show" src="' . Storage::url('images/product/' . $f->url) . '"></a>';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '<tr>';
        $html .= '<td>Nama Produk</td>';
        $html .= '<td>' . $product->name . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Kategori</td>';
        $html .= '<td>' . $category->name . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Short Name</td>';
        $html .= '<td>' . $product->code . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>SKU</td>';
        $html .= '<td>' . $product->sku . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Harga Jual</td>';
        $html .= '<td>' . number_format($product->price) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>COGS</td>';
        $html .= '<td>' . number_format($product->cost) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Satuan</td>';
        $html .= '<td>' . $product->unit . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Stok</td>';
        $html .= '<td>' . $product->quantity . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Jenis Produk</td>';
        if ($product->is_variant == 1) {
            $html .= '<td>Single Product</td>';
        } elseif ($product->is_variant == 2) {
            $html .= '<td>Product Variant</td>';
        }
        $html .= '</tr>';
        if ($product->is_variant == 2) {
            $varians = ProductVarian::where('product_id', $id)->get();
            $vp = '';
            $vp .= '<table class="table table-striped table-bordered">';
            $vp .= '<tr><td>Varian Group</td><td>Varian Name</td><td>SKU</td><td>Varian Price</td><td>Single Pick</td><td>Max Qty</td></tr>';
            foreach ($varians as $v) {
                $vp .= '<tr><td>' . $v->varian_group . '</td><td>' . $v->varian_name . '</td><td>' . $v->sku . '</td><td>' . number_format($v->varian_price) . '</td><td>' . $v->single_pick . '</td><td>' . $v->max_quantity . '</td></tr>';
            }
            $vp .= '</table>';

            $html .= '<tr>';
            $html .= '<td>Varian Product</td>';
            $html .= '<td>' . $vp . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>Model Produk</td>';
        if ($product->is_manufactured == 1) {
            $html .= '<td>Beli Jadi</td>';
        } elseif ($product->is_manufactured == 2) {
            $html .= '<td>Product Manufactured</td>';
        }
        $html .= '</tr>';
        if ($product->is_manufactured == 2) {
            $com = ProductComposition::where('product_id', $id)->get();

            $vp = '';
            $vp .= '<table class="table table-striped table-bordered">';
            $vp .= '<tr><td>Nama Bahan</td><td>Satuan</td><td>Quantity</td></tr>';
            foreach ($com as $c) {
                if ($c->product_type == 1) {
                    $material = Material::findorFail($c->material_id);
                    $nama_bahan = $material->material_name;
                    $satuan = $material->unit;
                } else {
                    $inter = InterProduct::findorFail($c->material_id);
                    $nama_bahan = $inter->product_name;
                    $satuan = $inter->unit;
                }

                $vp .= '<tr><td>' . $nama_bahan . '</td><td>' . $satuan . '</td><td>' . $c->quantity . '</td></tr>';
            }
            $vp .= '</table>';

            $html .= '<tr>';
            $html .= '<td>Varian Product</td>';
            $html .= '<td>' . $vp . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>Buffered Stock</td>';
            if ($product->buffered_stock == 1) {
                $html .= '<td>active</td>';
            } else {
                $html .= '<td>not active</td>';
            }
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>Stock Alert (minimum stock)</td>';
            $html .= '<td>' . $product->stock_alert . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>Keterangan</td>';
            $html .= '<td>' . $product->description . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $view = 'product-edit';
        $categories = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->get();
        $units = Unit::all();
        $product = Product::findorFail($id);
        $varians = ProductVarian::where('product_id', $id)->get();
        $group = ProductVarian::where('product_id', $id)->groupBy('varian_group')->get();
        $jumlah_varian = $varians->count();
        $komposisi = ProductComposition::where('product_id', $id)->get();
        $jumlah_group = $group->count();
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $inters = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();
        $gambar = ProductImages::where('product_id', $id)->get();
        $jumlah_komposisi = $komposisi->count();
        return view('main.product_edit', compact('view', 'categories', 'units', 'product', 'varians', 'group', 'jumlah_varian', 'jumlah_group', 'komposisi', 'materials', 'inters', 'jumlah_komposisi', 'gambar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $input = $request->all();
        // dd($input);
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

        if ($request->hasFile('image')) {
            $rules['image.*'] = 'max:500';
        }

        if ($request->is_variant == 2) {
            $rules['varian_name.*'] = 'required';
            $rules['varian_price.*'] = 'required';
            $rules['varian_group.*'] = 'required';
        }

        if ($request->is_manufactured == 2) {
            $rules['composition.*'] = 'required';
            $rules['quantity.*'] = 'required|numeric';
        } elseif ($request->is_manufactured == 1) {
            $rules['cost'] = 'required';
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

        $sku_cek = Product::where('user_id', $this->user_id_manage(session('id')))
            ->where('sku', $input['sku'])
            ->where('id', '!=', $id)
            ->count();

        if ($sku_cek > 0) {
            return response()->json([
                'success' => false,
                'message' => 'SKU sudah digunakan',
            ]);
        }

        try {
            $product = Product::findorFail($id);

            $input['cost'] = $request->is_manufactured == 2 ? $product->cost : $input['cost'];
            $input['default_cost'] = $request->is_manufactured == 2 ? $product->cost : $input['cost'];
            $input['stock_alert'] = empty($input['stock_alert']) ? 0 : $input['stock_alert'];
            $input['sell'] = 0;
            $input['created'] = date('Y-m-d H:i:s');
            $input['user_id'] = $this->user_id_manage(session('id'));
            $input['quantity'] = $product->quantity;
            $input['description'] = $input['desc_assist'];
            $input['price_ta'] = str_replace('.', '', $input['price_ta']) ?? 0;
            $input['price_mp'] = str_replace('.', '', $input['price_mp']) ?? 0;
            $input['price_cus'] = str_replace('.', '', $input['price_cus']) ?? 0;
            $product->update($input);

            $product_id = $id;

            if ($request->is_variant == 2) {
                ProductVarian::where('product_id', $product_id)->delete();

                for ($i = 0; $i < count($input['varian_name']); $i++) {
                    $new = new ProductVarian();
                    $new->product_id = $product_id;
                    $new->varian_group = $input['varian_group'][$i];
                    $new->varian_name = $input['varian_name'][$i];
                    $new->sku = $input['varian_sku'][$i];
                    $new->varian_price = $input['varian_price'][$i];
                    $new->single_pick = $input['single_pick'][$i];
                    $new->max_quantity = $input['max_quantity'][$i];
                    $new->created_at = now();
                    $new->updated_at = now();
                    $new->save();
                }
            }

            if ($request->is_manufactured == 2) {
                $cogs_awal = 0;
                ProductComposition::where('product_id', $id)->delete();
                for ($i = 0; $i < count($input['composition']); $i++) {
                    $c_value = explode('_', $input['composition'][$i]);
                    if ($c_value[1] == 1) {
                        $mat = Material::findorFail($c_value[0]);
                        $satuan = $mat->unit;
                        $cogs_awal = $cogs_awal + $mat->cost * $request->quantity[$i];
                    } else {
                        $inter = InterProduct::findorFail($c_value[0]);
                        $satuan = $inter->unit;
                        $cogs_awal = $cogs_awal + $inter->cost * $request->quantity[$i];
                    }
                    $com = new ProductComposition();
                    $com->material_id = $c_value[0];
                    $com->product_id = $product_id;
                    $com->unit = $satuan;
                    $com->quantity = $request->quantity[$i];
                    $com->product_type = $c_value[1];
                    $com->created_at = now();
                    $com->updated_at = now();
                    $com->save();
                }

                if ($request->created_by == 1) {
                    Product::where('id', $id)->update(['cost' => $cogs_awal]);
                }
            }

            if ($request->hasFile('image')) {
                $gambar = ProductImages::where('product_id', $product_id)->get();
                $dir = storage_path('app/public/images/product');
                if ($gambar->count() > 0) {
                    // foreach ($gambar as $g) {
                    //     $x_dir = $dir . '/' . $g->url;

                    //     if (file_exists($x_dir) && !empty($g->url)) {
                    //         unlink($x_dir);
                    //     }
                    // }

                    ProductImages::where('product_id', $product_id)->delete();
                }

                $count = count($_FILES['image']['name']);

                for ($i = 0; $i < $count; $i++) {
                    $unik = uniqid();

                    $extension = $request->file('image')[$i]->extension();
                    $img_name = $unik . date('dmyHis') . '.' . $extension;
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
                'success' => true,
                'message' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
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
        if ($cek1 > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hapus Produk Gagal karena produk sudah terdaftar pada Pembelian Produk Jadi',
            ]);
        }

        $cek2 = ProductManufacture::where('product_id', $id)->count();
        if ($cek2 > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hapus Produk Gagal karena produk sudah terdaftar pada Proses Produk Manufaktur',
            ]);
        }

        try {
            DB::beginTransaction();

            $cek_gambar = DB::table('md_product_images')->where('product_id', $id)->get();
            if ($cek_gambar->count() > 0) {
                // foreach ($cek_gambar as $cg) {
                //     $dir = storage_path('app/public/images/product/'.$cg->url);
                //     if (file_exists($dir) && !empty($cg->url)) {
                //         unlink($dir);
                //     }
                // }
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

    public function delete_multiple_product(Request $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();

            foreach ($input['id'] as $id) {
                $cek1 = ProductPurchaseItem::where('product_id', $id)->count();
                if ($cek1 > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hapus Produk Gagal karena produk sudah terdaftar pada Pembelian Produk Jadi',
                    ]);
                }

                $cek2 = ProductManufacture::where('product_id', $id)->count();
                if ($cek2 > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hapus Produk Gagal karena produk sudah terdaftar pada Proses Produk Manufaktur',
                    ]);
                }

                $cek_gambar = DB::table('md_product_images')->where('product_id', $id)->get();
                if ($cek_gambar->count() > 0) {
                    // foreach ($cek_gambar as $cg) {
                    //     $dir = storage_path('app/public/images/product/' . $cg->url);
                    //     if (file_exists($dir) && !empty($cg->url)) {
                    //         unlink($dir);
                    //     }
                    // }
                    DB::table('md_product_images')->where('product_id', $id)->delete();
                }
                DB::table('md_products')->where('id', $id)->delete();
                ProductVarian::where('product_id', $id)->delete();
                ProductComposition::where('product_id', $id)->delete();
            }

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

    public function product_export($param)
    {
        $param1 = str_replace('[', '', $param);
        $param2 = str_replace(']', '', $param1);
        $id = explode(',', $param2);

        // $data = Product::whereIn('id', $id)->get();
        $data = Product::where('user_id', $this->user_id_manage(session('id')))->get();
        return Excel::download(new ProductExport($data), 'product_list.xlsx');
    }

    public function get_bahan_product()
    {
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $inters = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();

        $html = '';

        $html .= '<div class="row baris" id="baris_1">';
        $html .= '<div class="col-md-8">';
        $html .= '<select class="form-control cust-control select-item"
        id="composition_1" name="composition[]">';
        $html .= '<option value="">Pilih komposisi bahan</option>';
        $html .= '<optgroup label="Bahan Baku">';
        foreach ($materials as $material) {
            $html .= '<option value="' . $material->id . '_1">' . $material->material_name . ' - ' . $material->unit . '</option>';
        }
        $html .= '</optgroup>';
        $html .= '<optgroup label="Barang Setengah Jadi">';
        foreach ($inters as $inter) {
            $html .= '<option value="' . $inter->id . '_2">' . $inter->product_name . ' - ' . $inter->unit . '</option>';
        }
        $html .= '</optgroup>';

        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="col-md-3">';
        $html .= '<input type="text" class="form-control cust-control"
        id="quantity_1" name="quantity[]"
        placeholder="quantitiy">';
        $html .= '</div>';
        $html .= '<div class="col-md-1">';
        $html .= '<center><a disabled="disabled" href="javascript:void(0);"
            class="avatar-text avatar-md bg-danger text-white"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"><i
                class="fa fa-trash"></i></a></center>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function ribuan($angka)
    {
        $angka_ribuan = number_format($angka);
        $angka_baru = str_replace(',', '.', $angka_ribuan);
        return $angka_baru;
    }

    public function open_product_add()
    {
        $category = ProductCategory::where('user_id', $this->user_id_manage(session('id')))->get();
        if ($category->count() > 0) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function getComposisitionEdit($id)
    {
        $product = Product::find($id);

        $data = ProductComposition::where('product_id', $id)->get();
        $materials = Material::where('userid', $this->user_id_manage(session('id')))->get();
        $inters = InterProduct::where('userid', $this->user_id_manage(session('id')))->get();

        $html = '';
        foreach ($data as $com_index => $com) {
            $option_material = '';
            $option_inter = '';

            if ($com->product_type == 1) {
                foreach ($materials as $material) {
                    $option_material .= '<option ' . ($material->id == $com->material_id ? 'selected' : '') . " value='$material->id" . '_1' . "'> $material->material_name - $material->unit</option>";
                }
            }
            if ($com->product_type == 2) {
                foreach ($inters as $inter) {
                    $option_inter .= '<option ' . ($inter->id == $com->material_id ? 'selected' : '') . " value='$inter->id" . '_1' . "'> $inter->product_name - $inter->unit</option>";
                }
            }

            $html .= "<div class='row baris mtop10 baris-tambahan'
                id='baris_$com_index'>
                <div class='col-md-8'>
                    <select
                        class='form-control cust-control select-item'
                        id='composition_$com_index'
                        name='composition[]'>
                        <option value=''>Pilih komposisi bahan</option>
                        <optgroup label='Bahan Baku'>
                            $option_material
                        </optgroup>
                        <optgroup label='Barang Setengah Jadi'>
                            $option_inter
                        </optgroup>
                    </select>
                </div>
                <div class='col-md-3'>
                    <input value='$com->quantity'
                        type='number'
                        class='form-control cust-control'
                        id='quantity_$com_index'
                        name='quantity[]' placeholder='quantitiy'>
                </div>
                <div class='col-md-1'>
                    <center><a
                            onclick='delete_composition_item($com_index)'
                            href='javascript:void(0);'
                            class='avatar-text avatar-md bg-danger text-white'
                            data-bs-toggle='dropdown'
                            data-bs-auto-close='outside'><i
                                class='fa fa-trash'></i></a></center>
                </div>
            </div>";
        }

        if ($product->is_manufactured == 2) {
            return response()->json($html);
        }

        return response()->json('');
    }

    public function store_display_change(Request $request)
    {
        $input = $request->all();

        $query = Product::where('id', $input['id'])->update([
            'store_displayed' => $input['nilai'],
        ]);

        return $query;
    }

    public function store_editable_change(Request $request)
    {
        $input = $request->all();

        $query = Product::where('id', $input['id'])->update([
            'is_editable' => $input['nilai'],
        ]);

        return $query;
    }

    public function use_stock(Request $request)
    {
        $input = $request->all();

        $query = Product::where('id', $input['id'])->update([
            'buffered_stock' => $input['nilai'],
        ]);

        return $query;
    }

    public function product_upload(Request $request)
    {
        try {
            $excel = new ProductImport();
            Excel::import($excel, $request->file);
            $total = $excel->get_total();

            if ($total > 2000) {
                return response()->json([
                    'success' => false,
                    'message' => 'file upload anda terdiri dari ' . $total . ' produk sedangkan upload yang diperbolehkan untuk 1x upload adalah 2.000 (dua ribu) produk',
                ]);
            } else {
                Excel::import(new ProductConfirmImport(), $request->file);
                return response()->json([
                    'success' => true,
                    'message' => 'success import file',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
