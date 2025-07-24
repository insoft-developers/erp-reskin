<?php

namespace App\Http\Controllers\StoreFront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DuitkuController;
use App\Http\Controllers\WalletLogsController;
use App\Http\Controllers\StoreFront\StoreController;
use App\Http\Controllers\StoreFront\ProcessController;
use Illuminate\Http\Request;
use App\Traits\DuitkuTrait;
use App\Models\Account;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\PenjualanProductVarian;
use App\Models\Branch;
use App\Models\QrCode;
use App\Models\MlUserInformation;
use App\Models\WalletLogs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class OrderController extends Controller
{
    use DuitkuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($username)
    {
        $footer = false;
        $view = $username . "/order";
        $cart = session()->get('cart', []);
        $qr = session()->get('qr');
        $meja = "";
        if ($qr != '') {
            $queryMeja = QrCode::where('qr_id', $qr)->select('no_meja')->first();
            $meja = $queryMeja->no_meja;
        }
        $totals = ProcessController::calculateTotals($cart, $username);
        $store = Account::where('username', $username)
            ->select('ml_accounts.id', 'ml_accounts.username', 'business_groups.branch_name', 'storefronts.template', 'storefronts.delivery')
            ->leftJoin('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')
            ->leftJoin('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')
            ->leftJoin('business_groups', 'ml_accounts.id', '=', 'business_groups.user_id')->first();
        $branches = Branch::where('account_id', $store->id)->get();
        if (count($branches) == 0) {
            $branches = array((object) array("id" => "0", "name" => $store->branch_name));
        }
        $order = session()->get('order', []);
        $previousUrl = session('previous_url', url('/'));
        return view('storefront.order', compact('cart', 'order', 'store', 'username', 'view', 'footer', 'totals', 'branches', 'qr', 'meja', 'previousUrl'));
    }

    public function getQrTable($username, $branch, $userid)
    {
        $user_id = Controller::get_owner_id($userid);
        if ($branch == 0) {
            $qr = QRCode::where('user_id', $user_id)->where('availability', 'Available')->get();
        } else {
            $qr = QRCode::where('branch_id', $branch)->where('availability', 'Available')->get();
        }
        return $qr;
    }
    public function updateOrderType(Request $request)
    {
        $order = session()->get('order', []);
        $order['order_type'] = $request->input('order_type');
        session()->put('order', $order);
        return response()->json(['success' => true]);
    }

    public function updateCustomerDetails(Request $request)
    {
        $order = session()->get('order', []);
        $order['customer_name'] = $request->input('customer_name');
        $order['phone_number'] = $request->input('phone_number');
        $order['payment_type'] = $request->input('payment_type');
        $order['qrTable'] = $request->input('qrTable');
        $order['branch_id'] = $request->input('branch_id');
        session()->put('order', $order);
        return response()->json(['success' => true, 'orderType' => $order['order_type']]);
    }
    public function updatePaymentDetails(Request $request)
    {
        $order = session()->get('order', []);
        $order['payment_type'] = $request->input('payment_type');
        session()->put('order', $order);
        return response()->json(['success' => true]);
    }
    public function confirmation($username, $orderid)
    {
        $footer = true;
        $view = $username . "/checkout";
        $store = Account::where('username', $username)
            ->select('ml_accounts.id', 'ml_accounts.username', 'ml_account_info.payment_method', 'ml_account_info.shipping')
            ->join('storefronts', 'ml_accounts.id', '=', 'storefronts.user_id')
            ->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')->first();
        $previousUrl = session('previous_url', url('/'));
        if ($store) {
            $order = Penjualan::where('id', $orderid)->first();
            if ($order) {
                if ($order->payment_method == 'kas') {
                    $payment = "Tunai";
                } elseif ($order->payment_method == 'randu-wallet') {
                    $payment = "Payment Gateway";
                } else {
                    $payment = "Transfer Bank";
                }
                return view('storefront.confirmation', compact('store', 'username', 'view', 'footer', 'order', 'payment', 'previousUrl'));
            }
        } else {
            $view = 'halaman-tidak-ditemukan';
            $username = 'notfound';
            $messageHeader = "Halaman tidak ditemukan!";
            $messageBody = "Silahkan hubungi kasir untuk mendapatkan informasi lebih lanjut";
            return view('storefront.page404', compact('view', 'messageHeader', 'messageBody', 'username', 'previousUrl'));
        }
    }
    public function payment($username, $order)
    {

        $penjualan = Penjualan::where('id', $order)->first();
        $penjualanProduct = PenjualanProduct::where('penjualan_id', $penjualan->id)
            ->select('penjualan_products.id', 'md_products.name', 'penjualan_products.price', 'penjualan_products.quantity', 'md_products.is_variant')
            ->leftJoin('md_products', 'md_products.id', '=', 'penjualan_products.product_id')->get();

        $productDetail = "Pembelian " . $penjualanProduct[0]->name . " - Ref: " . $penjualan->reference;
        $tax = $penjualan->tax;
        $discount = -$penjualan->discount;
        $itemDetails = [];

        // foreach ($penjualanProduct as $pp) {
        //     $variants = 0;
        //     if ($pp->is_variant == 2) {
        //         $variant = PenjualanProductVarian::where('penjualan_product_id', $pp->id)->get();
        //         foreach ($variant as $v) {
        //             $variants += ($v->price * $pp->quantity);
        //         }
        //     }
        //     array_push($itemDetails, ["name" => $pp->name, "price" => ((float)$pp->price * (int)$pp->quantity) + (float)$variants, "quantity" => (int)$pp->quantity]);
        // }
        //info dari CS Duitku, diskon bisa menggunakan minus, tapi dicoba masih gagal (malah jadi 0)
        // array_push($itemDetails, ["name" => "Diskon", "price" => $discount, "quantity" => 1]);
        // array_push($itemDetails, ["name" => "Pajak", "price" => (double)$penjualan->tax, "quantity" => 1]);
        // if($penjualan->shipping != 0){
        //     array_push($itemDetails, ["name" => "Ongkos Kirim", "price" => $penjualan->shipping, "quantity" => 1]);
        // }
        // ini yang digabung
        array_push($itemDetails, ["name" => $penjualanProduct[0]->name, "price" => $penjualan->paid, "quantity" => count($penjualanProduct)]);
        $currentAccount = Account::where('username', $username)->first();
        $detailAccount = MlUserInformation::where('user_id', $currentAccount->id)->first();
        $detailUser = [
            'email' => $currentAccount->email,
            'phone' => $currentAccount->phone ?? '0',
            'username' => $currentAccount->username,
            'fullname' => $currentAccount->fullname,
        ];
        $invoice = $this->createInvoice($productDetail, $itemDetails, route('order.success', ['username' => $username, 'order' => $order]), 'storefront', $detailUser);
        $result = $invoice['result'];
        if ($invoice['httpCode'] === 200) {
            $config = DB::table('ml_site_config')->first();
            WalletLogs::create([
                'user_id' => $currentAccount->id,
                'amount' => ($penjualan->paid * $config->fee_payment_gateway) / 100,
                'type' => '-',
                'from' => 'Storefront',
                'group' => 'transaction-fee',
                'note' => 'Transaksi Fee Randu Wallet - Storefront',
                'reference' => $result->reference,
                'status' => '0',
            ]);
            WalletLogs::create([
                'user_id' => $currentAccount->id,
                'amount' => $penjualan->paid,
                'type' => '+',
                'from' => 'Storefront',
                'group' => 'storefront',
                'note' => 'Pembayaran pesanan Sebesar Rp. ' . number_format($penjualan->paid, 0, ',', '.'),
                'reference' => $result->reference,
                'status' => '0',
            ]);
            Penjualan::where('id', $penjualan->id)->update([
                'payment_return_url' => $result->paymentUrl,
                'payment_start_at' => now(),
                'flip_ref' => $result->reference
            ]);
            return redirect()->away($result->paymentUrl);
        }
        // dd($result);
        return redirect()->back()->withErrors(['message' => $result]);
    }
    public function success($username, $order)
    {
        $view = $username . "/payment/success";
        $footer = false;
        $order = Penjualan::where('id', $order)->first();
        $message = "Order tidak ditemukan!";
        $status = false;
        $data = [];
        if ($order) {
            $message = "";
            $status = true;
            $data = $order;
        }
        return view('storefront.success', compact('view', 'message', 'status', 'order', 'username', 'footer'));
    }
    public function calculateFinal(Request $request)
    {
        $footer = false;
        $cart = session()->get('cart', []);
        $order = session()->get('order', []);
        $username = $request->username;
        $order["cust_alamat"]     = $request->address;
        $order["cust_provinsi"]     = $request->province;
        $order["cust_provinsi_id"]     = $request->province_id;
        $order["cust_kota"]     = $request->city;
        $order["cust_kota_id"]     = $request->city_id;
        $order["cust_kecamatan"]     = $request->subdistrict;
        $order["cust_kecamatan_id"]     = $request->subdistrict_code;
        $order["shipping_code"]     =  $request->code;
        $order["shipping_service"]  =  $request->service;
        $order["shipping_cost"]     =  (float)$request->cost;
        session()->put('order', $order);
        $order = session()->get('order', []);
        $totals = ProcessController::calculateTotals($cart, $username);
        if($totals){
            return response()->json(['success' => true, 'totals' => $totals]);
        }
        response()->json(['success' => false, 'totals' => []]);
    }
}
