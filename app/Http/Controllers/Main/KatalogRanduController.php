<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CategoryProduct;
use App\Models\MlUserInformation;
use App\Models\ProductKatalog;
use App\Models\Setting;
use App\Models\ShippingSetting;
use App\Models\TransactionProduct;
use App\Models\TransactionProductDetail;
use App\Models\Voucher;
use App\Traits\DuitkuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KatalogRanduController extends Controller
{
    use DuitkuTrait;

    public function index(Request $request)
    {
        $view = 'katalog_randu';
        $keyword = $request->search;
        $category = $request->category_id;
        $category_product = $this->categoryCatalog();
        $data = ProductKatalog::orderBy('id', 'desc')
                                ->when($keyword, function ($query) use ($keyword) {
                                    $query->where('name', 'like', "%{$keyword}%");
                                })
                                ->when($category, function ($query) use ($category) {
                                    $query->where('category_product_id', $category);
                                })
                                ->get();

        return view('main.katalog_randu.index', compact('view', 'data', 'category_product'));
    }

    public function show($id)
    {
        $view = 'katalog_randu';
        $data = ProductKatalog::find($id);

        return view('main.katalog_randu.show', compact('view', 'data'));
    }
    
    public function addToCart($id)
    {
        $product = ProductKatalog::find($id);
        $cart = session()->get('cartKatalogProduct', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "id" => $product->id,
                'image' => $product->image,
                "name" => $product->name,
                "quantity" => 1,
                "selling_price" => $product->selling_price,
                "weight" => $product->weight
            ];
        }

        session()->put('cartKatalogProduct', $cart);

        $sumQty = array_sum(array_column(session()->get('cartKatalogProduct', []), 'quantity'));

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang!',
            'item_count' => $sumQty,
        ]);
    }

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cartKatalogProduct');
        $cart[$id]['quantity'] = $request->quantity;
        session()->put('cartKatalogProduct', $cart);
        
        $data = $this->checkTotal();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil di ubah!',
            'item_count' => $data['sumQty'],
            'sub_total_product' => 'Rp. ' . number_format($request->quantity * $cart[$id]['selling_price']),
            'sub_total' => 'Rp. '.number_format($data['sub_total']),
            'pajak' => 'Rp. '.number_format($data['pajak']),
            'ongkir' => 'Rp. '.number_format($data['ongkir']),
            'diskon' => 'Rp. '.number_format($data['diskon']),
            'total' => 'Rp. '.number_format($data['total']),
        ]);
    }

    public function removeToCart($id)
    {
        $cart = session()->get('cartKatalogProduct');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cartKatalogProduct', $cart);
        }

        $data = $this->checkTotal();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil di hapus dari keranjang!',
            'item_count' => $data['sumQty'],
            'sub_total' => 'Rp. '.number_format($data['sub_total']),
            'pajak' => 'Rp. '.number_format($data['pajak']),
            'ongkir' => 'Rp. '.number_format($data['ongkir']),
            'diskon' => 'Rp. '.number_format($data['diskon']),
            'total' => 'Rp. '.number_format($data['total']),
        ]);
    }

    public function create()
    {
        $config = Setting::first();
        $shipping = ShippingSetting::first()->shipping ?? null;

        $data = $this->checkTotal();
        $data['shipping'] = null;
        if ($shipping != null) {
            $data['shipping'] = collect(json_decode($shipping, true)) ->filter(function ($item) {
                return $item['selected'] === 'true';
            });
        }

        return view('main.katalog_randu.cart', compact('data'));
    }

    public function checkVoucher(Request $request)
    {
        $data = $request->all();
        $voucher = Voucher::where('code', $data['voucher'])->where('expired_at', '>=', date('Y-m-d H:i:s'))->where('max_use', '>', 0)->first();
        if ($voucher == null) {
            return response()->json([
                'status' => false,
                'message' => 'Kode Voucher Tidak Valid'
            ]);
        }
        
        $data = $this->checkTotal($voucher);

        return response()->json([
            'status' => true,
            'message' => 'Kode Voucher Valid',
            'diskon' => 'Rp. '.number_format($data['diskon']),
            'total' => 'Rp. '.number_format($data['total']),
        ]);
    }

    public function checkTotal($voucher = null)
    {
        $config = Setting::first();

        $data['cart'] = session()->get('cartKatalogProduct', []);
        $data['sub_total'] = 0;

        foreach ($data['cart'] as $key => $value) {
            $data['sub_total'] += ($value['quantity'] * $value['selling_price']);
        }

        $data['pajak'] = $data['sub_total'] * $config->pajak_randu / 100;
        $data['ongkir'] = 0;
        $data['diskon'] = 0;

        $total = $data['sub_total'] + $data['pajak'] + $data['ongkir'];

        if (isset($voucher)) {
            if ($voucher->type == 'persen') {
                $data['diskon'] = ($total * $voucher->value) / 100;
            } else {
                $data['diskon'] = $voucher->value;
            }
        }

        $data['sumQty'] = array_sum(array_column(session()->get('cartKatalogProduct', []), 'quantity'));
        $data['sumWeight'] = array_sum(array_column(session()->get('cartKatalogProduct', []), 'weight'));
        $data['total'] = $data['sub_total'] + $data['pajak'] + $data['ongkir'] - $data['diskon'];

        return $data;
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $data['product'] = $this->checkTotal();

        if (isset($data['voucher'])) {
            $voucher = Voucher::where('code', $data['voucher'])->where('expired_at', '>=', date('Y-m-d H:i:s'))->where('max_use', '>', 0)->first();
            if ($voucher == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kode Voucher Sudah Kedaluwarsa'
                ]);
            }

            $voucher->update([
                'max_use' => $voucher->max_use - 1
            ]);

            $data['discount'] = (int)str_replace(['Rp.', ','], '', $data['diskon']);
        }

        return $this->atomic(function () use ($data) {
            $create = TransactionProduct::create([
                'references' => 'KR-'.now()->format('YmdHis').rand(100, 999),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'user_id' => session('id'),
                'province_id' => $data['province_id'],
                'city_id' => $data['city_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'shipping' => $data['shipping'],

                'status_transaction' => 0,
                'status_payment' => 0,

                'total_qty' => $data['product']['sumQty'],
                'sub_total' => $data['product']['sub_total'],
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['product']['pajak'],
                'ongkir' => $data['ongkir'],
                'total_price' => $data['product']['total'] + $data['ongkir'] + $data['product']['pajak'] - ($data['discount'] ?? 0),
            ]);

            $itemsDetails = [
                [
                    'name' => 'Randu - ' . $create->references,
                    'price' => $create['total_price'],
                    'quantity' => 1
                ],
            ];

            foreach ($data['product']['cart'] as $key => $value) {
                TransactionProductDetail::create([
                    'transaction_product_id' => $create->id,
                    'product_id' => $value['id'],
                    'qty' => $value['quantity'],
                    'price' => $value['selling_price'],
                    'total' => $value['quantity'] * $value['selling_price'],
                ]);
            }

            $currentAccount = Account::where('id', session()->get('id'))->first();
            $detailUser = [
                'email' => $currentAccount->email,
                'phone' => $create->phone ?? '',
                'username' => $currentAccount->username,
                'fullname' => $currentAccount->fullname,
            ];

            $invoice = $this->createInvoice($create->references, $itemsDetails, route('katalog-randu.index'), 'katalog-randu', $detailUser);
            $result = $invoice['result'];
            if ($invoice['httpCode'] === 200) {
                session()->forget('cartKatalogProduct');
                $create->flip_ref = $result->reference;
                $create->save();

                return redirect()->away($result->paymentUrl);
            }
            return redirect()->back()->with('success', 'Transaksi dengan no invoice '.$create->references.' Berhasil Dibuat');
        });
    }

    // API KEY RAJA ONGKIR = 32acfec0aa49b3c9121d6bb185b8b59b
    public function cekOngkir(Request $request)
    {
        $warehouse = ShippingSetting::first();
        $weight = $this->checkTotal()['sumWeight'];
        
        $data = $request->all();
        $response = Http::withHeaders([
            'content-type' => 'application/x-www-form-urlencoded',
            'key' => env('RAJAONGKIR_API_KEY'),
        ])->asForm()->post('https://api.rajaongkir.com/starter/cost', [
            'origin' => $warehouse->city_id,
            'destination' => $data['city_id'],
            'weight' => $weight,
            'courier' => strtolower($data['courier']),
        ]);
        
        $data = $response->json();

        return $data;
    }

    public function categoryCatalog()
    {
        $columns = [
            'id',
            'code',
            'name',
        ];

        $data = CategoryProduct::orderBy('name', 'asc')
                    ->select($columns)
                    ->get();
        
        return $data;
    }
}
