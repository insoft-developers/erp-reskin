<?php

namespace App\Http\Controllers\StoreFront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StoreFront\CartController;
use App\Http\Controllers\StoreFront\ProcessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Storefront;
use App\Models\Account;
use App\Models\QrCode;
use App\Models\Product;
use App\Models\ProductVarian;
use App\Models\ProductCategory;
use App\Models\ProductImages;
use App\Traits\DeliveryTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StorefrontController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use DeliveryTrait;
    public function index($username)
    {
        $view = $username;
        $title = "Storefront";
        $footer = true;
        $qrcode = false;
        $userCek = Account::where('username', $username)->first();
        if (!$userCek) {
            $messageHeader = "Username tidak ditemukan";
            $messageBody = "Pastikan URL yang kamu akses benar";
            return view('storefront.page404', compact('messageHeader', 'messageBody', 'username'));
        }
        $store = Account::where('username', $username)->select(
            'ml_accounts.id',
            'ml_accounts.username',
            'ml_account_info.payment_method',
            'ml_account_info.shipping',
            'business_groups.branch_name',
            'ml_account_info.store_address',
            'business_groups.business_address',
            'storefronts.banner_image1',
            'storefronts.banner_image2',
            'storefronts.banner_image3',
            'storefronts.banner_link1',
            'storefronts.banner_link2',
            'storefronts.banner_link3',
            'storefronts.template'
        )
            ->join('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')
            ->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')
            ->join('business_groups', 'ml_accounts.id', '=', 'business_groups.user_id')->first();


        if ($store) {
            $categories = ProductCategory::where('user_id', $store['id'])->limit(5)->get();
            $onSale = Product::where('user_id', $store['id'])->where('store_displayed', 1)
                ->join('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')->get();
            $products = Product::with(['productImages'])
                ->where('md_products.store_displayed', 1)
                ->where('md_products.user_id', $store['id'])->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_products.description', 'md_product_category.name as category_name','md_products.quantity','md_products.buffered_stock')
                ->leftJoin('md_product_category', 'md_product_category.id', '=', 'md_products.category_id')->get();
            $trend = Product::where('user_id', $store['id'])->where('store_displayed', 1)->where('sell', '>', 0)->orderBy('sell', 'desc')->limit(10)
                ->join('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')->get();
            $qr = session()->put('qr', '');
            $varian = ProductVarian::all();
            return view('storefront.index', compact('title', 'view', 'username', 'store', 'onSale', 'products', 'trend', 'categories', 'footer', 'varian', 'qrcode'));
        } else {
            $title = "Storefront Belum Aktif";
            $messageHeader = "Storefront kamu belum aktif!";
            $messageBody = "Silakan lengkapi dulu datanya melalui menu <strong style='font-weight:700'>Pengaturan Storefront</strong> dan <strong style='font-weight:700'>Pengaturan Pembayaran</strong>";
            return view('storefront.page404', compact('title', 'messageHeader', 'messageBody', 'username'));
        }
    }
    public function reservationByQrCode($username, $qrcode)
    {
        $view = $username . '/' . $qrcode;
        $title = "Storefront QR";
        $footer = true;
        $userCek = Account::where('username', $username)->first();
        if (!$userCek) {
            $messageHeader = "Username tidak ditemukan";
            $messageBody = "Pastikan URL yang kamu akses benar";
            return view('storefront.page404', compact('messageHeader', 'messageBody', 'username'));
        }
        $qrCheck = QrCode::where('qr_id', $qrcode)->first();
        if ($qrCheck) {
            if ($qrCheck->availability == 'Reserved') {
                session()->forget('cart');
                session()->forget('qr');
                return view('storefront.qr-reserved', compact('username'));
            }
            $store = Account::where('username', $username)
                ->select(
                    'ml_accounts.id',
                    'ml_accounts.username',
                    'ml_account_info.payment_method',
                    'ml_account_info.shipping',
                    'branches.name as branch_name',
                    'ml_account_info.store_address',
                    'branches.address as business_address',
                    'storefronts.banner_image1',
                    'storefronts.banner_image2',
                    'storefronts.banner_image3',
                    'storefronts.banner_link1',
                    'storefronts.banner_link2',
                    'storefronts.banner_link3',
                    'storefronts.template'
                )
                ->join('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')
                ->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')
                ->join('branches', 'ml_accounts.id', '=', 'branches.account_id')->first();
            if ($store) {

                $categories = ProductCategory::where('user_id', $store['id'])->limit(5)->get();
                $onSale = Product::where('user_id', $store['id'])
                    ->join('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')->get();
                $trend = Product::where('user_id', $store['id'])->where('sell', '>', 0)->orderBy('sell', 'desc')->limit(10)
                    ->join('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')->get();
                // $products = Product::where('user_id', $store['id'])->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_products.description', 'md_product_images.url')
                //     ->leftJoin('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')
                //     ->leftJoin('md_product_category', 'md_product_category.id', '=', 'md_products.category_id')->get();

                // JIKA TERDAPAT ERROR MAKA AKTIFKAN CODE DIATAS
                $products = Product::where('md_products.user_id', $store['id'])
                    ->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_products.description', 'md_product_images.url')
                    ->leftJoin('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')
                    ->leftJoin('md_product_category', 'md_product_category.id', '=', 'md_products.category_id')
                    ->groupBy('md_products.id', 'md_product_images.url', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_products.description')
                    ->get();

                $meja = QrCode::where('qr_id', $qrcode)->first();
                $cart = session()->get('cart', []);
                $qr = session()->put('qr', $qrcode);
                $varian = ProductVarian::all();
                if (!$meja) {
                    $messageHeader = "Meja tidak ditemukan!";
                    $messageBody = "Silahkan hubungi kasir untuk mendapatkan nomor meja yang lain";
                    return view('storefront.page404', compact('messageHeader', 'messageBody', 'username'));
                }

                return view('storefront.qr-order', compact('title', 'view', 'username', 'store', 'meja', 'onSale', 'trend', 'products', 'categories', 'footer', 'varian', 'qrcode'));
            } else {
                $title = "Storefront Belum Aktif";
                $messageHeader = "Storefront kamu belum aktif!";
                $messageBody = "Silakan lengkapi dulu datanya melalui menu <strong style='font-weight:700'>Pengaturan Storefront</strong> dan <strong style='font-weight:700'>Pengaturan Pembayaran</strong>";
                return view('storefront.page404', compact('title', 'messageHeader', 'messageBody', 'username'));
            }
        } else {
            session()->forget('cart');
            session()->forget('qr');
            $messageHeader = "Meja tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan nomor meja yang lain";
            return view('storefront.page404', compact('messageHeader', 'messageBody', 'username'));
        }
    }
    public function setting()
    {
        $view = "storefront/setting";
        $setting = Storefront::where('user_id', session('id'))->first();
        $account = Account::where('id', session('id'))->first();
        $info = DB::table('ml_account_info')->where('user_id', session('id'))->first();
        $provinces = DB::table('ro_provinces')->get();
        $cities = DB::table('ro_cities')->get();
        $subdistricts = DB::table('ro_subdistricts')->get();
        if ($info && $info->payment_method != 'null') {
            $payment = base64_encode($info->payment_method);
        } else {
            $payment = null;
        }
        if ($info && $info->shipping != 'null') {
            $shipping = base64_encode($info->shipping);
        } else {
            $shipping = null;
        }
        return view('storefront.setting', compact('view', 'setting', 'account', 'info', 'payment', 'shipping', 'provinces', 'cities', 'subdistricts'));
    }
    public function getCity($province)
    {
        $cities = $this->getData('city', '', $province);
        return $cities;
    }
    public function getSubdistrict($city)
    {
        $subdistricts = $this->getData('subdistrict', '', $city);
        return $subdistricts;
    }
    public function getShippingCost(Request $request)
    {

        $data = [
            "origin" => $request->origin,
            "originType" => $request->originType,
            "destination" => $request->destination,
            "destinationType" => $request->destinationType,
            "weight" => (float)$request->weight,
            "courier" => $request->courier
        ];
        $cost = $this->getCost($data);
        if ($cost['status'] === 'success') {
            return $cost['data'];
        } else {
            return response()->json([
                'message' => $cost['message'],
            ], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // dd($input);
        $rules = [];
        if ($request->hasFile('img1')) {
            $rules['img1'] = "required|image|max:512";
        }
        if ($request->hasFile('img2')) {
            $rules['img2'] = "required|image|max:512";
        }
        if ($request->hasFile('img3')) {
            $rules['img3'] = "required|image|max:512";
        }


        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response = ['title' => 'Gagal', 'text' => 'Gambar Banner Maksimal berukuran 512 KB', 'icon' => 'error'];
            return response()->json($response);
        }


        $user_id = session('id');
        $owner_id = Controller::get_owner_id($user_id);
        $shipping = json_decode($request->shippingData, JSON_PRETTY_PRINT);
        $find = Storefront::where('user_id', $owner_id)->first();
        $account = DB::table('ml_account_info')->where('user_id', $owner_id)->first();

        if ($find) {
            $img_name1 = $find->banner_image1;
            $img_name2 = $find->banner_image2;
            $img_name3 = $find->banner_image3;

            if ($request->hasFile('img1')) {
                $img1 = $request->file('img1');
                $extension1 = $request->file('img1')->extension();
                $img_name1 = 'storefront-banner1-' . date('dmyHis') . '.' . $extension1;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img1'), $img_name1);
            }

            if ($request->hasFile('img2')) {
                $img2 = $request->file('img2');
                $extension2 = $request->file('img2')->extension();
                $img_name2 = 'storefront-banner2-' . date('dmyHis') . '.' . $extension2;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img2'), $img_name2);
            }

            if ($request->hasFile('img3')) {
                $img3 = $request->file('img3');
                $extension3 = $request->file('img3')->extension();
                $img_name3 = 'storefront-banner3-' . date('dmyHis') . '.' . $extension3;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img3'), $img_name3);
            }
            $find->banner_image1 = $img_name1;
            $find->banner_link1 = $request->img_link1 ? $request->img_link1 : '';
            $find->banner_image2 = $img_name2;
            $find->banner_link2 = $request->img_link2 ? $request->img_link2 : '';
            $find->banner_image3 = $img_name3;
            $find->banner_link3 = $request->img_link3 ? $request->img_link3 : '';
            $find->template = $request->template;
            $find->theme_color = $request->theme_color;
            $find->delivery = $request->delivery;
            $find->checkout_whatsapp = $request->checkout_whatsapp;
            $find->template_order_info = $request->template_order_info;
            $find->whatsapp_number = $request->whatsapp_number;
            $save = $find->save();
        } else {
            $img_name1 = '';
            $img_name2 = '';
            $img_name3 = '';

            if ($request->hasFile('img1')) {
                $img1 = $request->file('img1');
                $extension1 = $request->file('img1')->extension();
                $img_name1 = 'storefront-banner1-' . date('dmyHis') . '.' . $extension1;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img1'), $img_name1);
            }

            if ($request->hasFile('img2')) {
                $img2 = $request->file('img2');
                $extension2 = $request->file('img2')->extension();
                $img_name2 = 'storefront-banner2-' . date('dmyHis') . '.' . $extension2;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img2'), $img_name2);
            }

            if ($request->hasFile('img3')) {
                $img3 = $request->file('img3');
                $extension3 = $request->file('img3')->extension();
                $img_name3 = 'storefront-banner3-' . date('dmyHis') . '.' . $extension3;
                $path = Storage::putFileAs('public/images/storefront/banners', $request->file('img3'), $img_name3);
            }
            $save = Storefront::create([
                "user_id" => $user_id,
                "template" => $request->template,
                "theme_color" => $request->theme_color,
                "banner_image1" => $img_name1,
                "banner_link1" => $request->img_link1 ? $request->img_link1 : '',
                "banner_image2" => $img_name2,
                "banner_link2" => $request->img_link2 ? $request->img_link2 : '',
                "banner_image3" => $img_name3,
                "banner_link3" => $request->img_link3 ? $request->img_link3 : '',
                "checkout_whatsapp" => $request->checkout_whatsapp ? $request->checkout_whatsapp : null,
                "template_order_info" => $request->template_order_info ? $request->template_order_info : '',
                "whatsapp_number" => $request->whatsapp_number ? $request->whatsapp_number : ''
            ]);
        }
        if ($account) {

            $accounts = DB::table('ml_account_info')->where('user_id', $owner_id)->update([
                "store_address" => $request->address,
                "province_id" => $request->province_id,
                "province_name" => $request->province_name,
                "city_id" => $request->city_id,
                "city_name" => $request->city_name,
                "subdistrict_id" => $request->subdistrict_id,
                "subdistrict_name" => $request->subdistrict_name,
                "shipping" => $shipping
            ]);
        } else {
            $accounts = DB::table('ml_account_info')->insert([
                "user_id" => $user_id,
                "store_address" => $request->address,
                "province_id" => $request->province_id,
                "province_name" => $request->province_name,
                "city_id" => $request->city_id,
                "city_name" => $request->city_name,
                "subdistrict_id" => $request->subdistrict_id,
                "subdistrict_name" => $request->subdistrict_name,
                "shipping" => $shipping
            ]);
        }

        $uname = DB::table('ml_accounts')->where('id', $user_id)->update([
            "username" => $request->username
        ]);
        if ($save || $accounts || $uname) {
            $response = ['title' => 'Berhasil', 'text' => 'Data Berhasil Disimpan', 'icon' => 'success'];
        } else {
            $response = ['title' => 'Gagal', 'text' => 'Data Gagal Disimpan', 'icon' => 'error'];
        }
        return response()->json($response);
    }

    public function usernameChecker(Request $request)
    {
        $id = $request->id;
        $username = $request->username;
        $check = Account::where('username', $username)->where('id', '!=', $id)->first();
        if ($check || $username == 'admin') {
            return 'not available';
        } else {
            return 'available';
        }
    }

    public function detailProduct($username, $idprod)
    {
        $view = $username . '/p/' . $idprod;
        $footer = false;
        $product = Product::with(['productImages'])->where('store_displayed', 1)->where('md_products.id', $idprod)
            ->leftJoin('md_product_category', 'md_product_category.id', '=', 'md_products.category_id')
            ->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.description', 'md_products.unit', 'md_products.is_variant', 'md_products.weight', 'md_products.user_id', 'md_product_category.name as category','md_products.quantity','md_products.buffered_stock')
            ->first();
        $productImages = ProductImages::where('product_id', $idprod)->get();
        $userID = $product->user_id;
        $template = Storefront::whereUserId($userID)->first()->template;
        if ($product) {
            $productVars = session()->get('variants', []);
            if ($product->is_variant == 2) {
                $variants = ProductVarian::where('product_id', $idprod)->get()->groupBy('varian_group');
            } else {
                $variants = [];
            }
            $title = $product->name;
            if ($template == 'FNB') {
                return view('storefront.detail-product', compact('view', 'title', 'product', 'footer', 'variants', 'productVars', 'username'));
            } else {
                return view('storefront.template.themes.nonfnb.pages.detail-product', compact('view', 'title', 'product', 'footer', 'variants', 'productVars', 'username', 'productImages'));
            }
        } else {
            $title = "Produk tidak ditemukan!";
            $messageHeader = "Produk tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
            return view('storefront.page404', compact('view', 'title', 'messageHeader', 'messageBody', 'username'));
        }
    }
    public function page404()
    {
        $view = 'halaman-tidak-ditemukan';
        $username = 'notfound';
        $messageHeader = "Halaman tidak ditemukan!";
        $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
        return view('storefront.page404', compact('view', 'messageHeader', 'messageBody', 'username'));
    }
    public function categories($username)
    {
        $view = $username . '/categories';
        $store = Account::where('username', $username)->first();
        $footer = true;
        if ($store) {
            $getId = Controller::get_owner_id($store['id']);
            $categories = ProductCategory::where('user_id', $getId)->get();
            $template = Storefront::where('user_id', $getId)->first()->template;
            if ($template == 'FNB') {
                return view('storefront.categories', compact('view', 'footer', 'categories', 'username'));
            } else {
                return view('storefront.template.themes.nonfnb.pages.categories', compact('view', 'footer', 'categories', 'username'));
            }
        } else {
            $messageHeader = "User tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
            return view('storefront.page404', compact('view', 'messageHeader', 'messageBody', 'username'));
        }
    }
    public function productCategory($username, $category)
    {
        $view = $username . '/categories';
        $store = Account::where('username', $username)->first();
        $footer = true;
        if ($store) {
            $getId = Controller::get_owner_id($store['id']);
            $varian = ProductVarian::all();
            $products = Product::with(['productImages'])->where('store_displayed', 1)->where('category_id', $category)
                ->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_products.description', 'md_products.quantity','md_products.buffered_stock')
                ->get();
            $template = Storefront::where('user_id', $getId)->first()->template;
            if ($template == 'FNB') {
                return view('storefront.product-list', compact('view', 'footer', 'products', 'username', 'varian'));
            } else {
                return view('storefront.template.themes.nonfnb.pages.product-list', compact('view', 'footer', 'products', 'username', 'varian'));
            }
        } else {
            $messageHeader = "User tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
            return view('storefront.page404', compact('view', 'messageHeader', 'messageBody', 'username'));
        }
    }
    public function search($username, $product)
    {
        $view = $username . '/search';
        $store = Account::where('username', $username)->first();
        $user_id = Controller::get_owner_id($store->id);
        $footer = true;
        if ($store) {
            $getId = Controller::get_owner_id($store['id']);
            $varian = ProductVarian::all();
            $products = Product::where('md_products.name', 'like', '%' . $product . '%')
                ->where('md_products.user_id', $user_id)
                ->where('md_products.store_displayed', 1)
                ->select('md_products.id', 'md_products.name', 'md_products.price', 'md_products.is_variant', 'md_product_images.url', 'md_products.description')
                ->leftJoin('md_product_images', 'md_products.id', '=', 'md_product_images.product_id')->get();
            return view('storefront.search', compact('view', 'footer', 'products', 'username', 'varian', 'product'));
        } else {
            $messageHeader = "User tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
            return view('storefront.page404', compact('view', 'messageHeader', 'messageBody', 'username'));
        }
    }
    public function about($username)
    {
        $footer = true;
        $store = Account::where('username', $username)
            ->select('business_groups.branch_name', 'business_groups.business_address', 'business_groups.company_email')
            ->join('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')
            ->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')
            ->join('business_groups', 'ml_accounts.id', '=', 'business_groups.user_id')->first();
        if ($store) {
            return view('storefront.about', compact('username', 'store', 'footer'));
        }
        $messageHeader = "Username tidak ditemukan";
        $messageBody = "Pastikan URL yang kamu akses benar";
        return view('storefront.page404', compact('messageHeader', 'messageBody', 'username'));
    }
}
