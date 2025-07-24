<?php

namespace App\Http\Controllers\StoreFront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StoreFront\ProcessController;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVarian;
use App\Models\Storefront;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index($username)
    {
        $footer = false;
        $cart = session()->get('cart', []);
        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $variants = ProcessController::parseVariants($cart);
        $previousUrl = session('previous_url', url('/'));
        if (!session()->has('order')) {
            session()->put('order', [
                'order_type' => 'dine_in',
                'customer_name' => '',
                'phone_number' => '',
                'payment_type' => ''
            ]);
            $order = session()->get('order');

        }else{
            $order = session()->get('order');
            $order['order_type'] = 'dine_in';
            $order['customer_name'] = '';
            $order['phone_number'] = '';
            $order['payment_type'] = '';
            session()->put('order', $order);
        }

        $order = session()->get('order');
        $totals = ProcessController::calculateTotals($cart, $username);
        $store = Account::where('username', $username)->first();
        $storeFront = Storefront::where('user_id',$store->id)->first();
        return view('storefront.cart', compact('cart','store','username', 'totalQuantity', 'totals', 'footer', 'order', 'variants', 'previousUrl','storeFront'));
    }
    public function data($username)
    {
        $footer = false;
        $cart = session()->get('cart', []);
        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $totals = ProcessController::calculateTotals($cart, $username);
        $variants = ProcessController::parseVariants($cart);
        if (!session()->has('order')) {
            session()->put('order', [
                'order_type' => 'dine_in',
                'customer_name' => '',
                'phone_number' => '',
                'payment_type' => ''
            ]);
            $order = session()->get('order');

        }else{
            $order = session()->get('order');
            $order['order_type'] = 'dine_in';
            $order['customer_name'] = '';
            $order['phone_number'] = '';
            $order['payment_type'] = '';
            session()->put('order', $order);
        }
        $order = session()->get('order');

        return response()->json(['status' => 'Data Fetch', 'cart' => $cart, 'totalQuantity' => $totalQuantity, 'totals' => $totals, 'variants' => $variants]);
    }
    public function add(Request $request)
    {
        // dd($request->all());
        
        $product = $request->input('product');
        
        
        
        $cart_content = session('cart') ?? [] ;
       
        $cari = array_search($product['id'], array_column($cart_content, 'id'));
        if($cari !== false) {
            $cart_qty = $cart_content[$product['id']]['quantity'];
        } else {
            $cart_qty = 0;
        }
        

        $jumlah_order = (int)$product['quantity'];
        
        $total_order = $cart_qty + $jumlah_order;
        $barang = Product::findorFail($product['id']);

        $stok_sekarang = $barang->quantity;
        $alert = $barang->stock_alert == null ? 0 : $barang->stock_alert;
        
        $tersedia = $stok_sekarang - $alert;

        if($barang->buffered_stock == 1 && $tersedia < $total_order ) {
            return response()->json(['status' => 'failed', 'message'=> 'Maaf Stok tidak cukup...!']);
        }
       


        $quantity = (int)$request->input('quantity');
        $username = $request->input('username');
        $cart = session()->get('cart', []);
        

        if (isset($cart[$product['id']])) {
                
            $product['unik'] = uniqid();
            if(! array_key_exists("variants", $product)) {
                $cart[$product['id']]['quantity'] = (int)$cart[$product['id']]['quantity'] + $quantity;
            } else {
                $jumlah_varian = count($product['variants']);
                array_push($cart, $product);
            } 
        } else {
            $product['quantity'] = $quantity;
            $cart[$product['id']] = $product;
        
        }
    
        session()->put('cart', $cart);

       
        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $totals = ProcessController::calculateTotals($cart, $username);

        return response()->json(['status' => 'Product added to cart', 'totalQuantity' => $totalQuantity, 'totals' => $totals]);
    }

    public function remove(Request $request)
    {
        
        
        $productId = $request->input('productId');
        $username = $request->input('username');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $totals = ProcessController::calculateTotals($cart, $username);
        if($totalQuantity == 0){
            session()->forget('cart');
            session()->forget('order');
        }
        return response()->json(['status' => 'Product removed from cart', 'totalQuantity' => $totalQuantity, 'totals' => $totals]);
    }

    public function updateQuantity(Request $request)
    {
        
        
        
        $productId = $request->input('productId');
        $quantity = $request->input('quantity');
        $username = $request->input('username');
        $cart = session()->get('cart', []);
        

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $totals = ProcessController::calculateTotals($cart, $username);
        if($totalQuantity == 0){
            session()->forget('cart');
        }
      
        return response()->json(['status' => 'Product quantity updated', 'totalQuantity' => $totalQuantity, 'totals' => $totals]);
    }
    public function updateNotes(Request $request)
    {
        $productId = $request->input('productId');
        $notes = $request->input('notes');
        $username = $request->input('username');
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['notes'] = $notes;
            session()->put('cart', $cart);
        }

        $totalQuantity = ProcessController::getTotalQuantity($cart);
        $totals = ProcessController::calculateTotals($cart, $username);

        return response()->json(['status' => 'Product notes updated', 'totalQuantity' => $totalQuantity, 'totals' => $totals]);
    }
    public function applyVoucher(Request $request)
    {
        $voucher = $request->input('voucher');
        $username = $request->input('username');

        $date = Carbon::today();
        $order = session()->get('order', []);
        $discount = Discount::where('code', $voucher)->where('expired_at', '>', $date)->first();
        if($discount){
            $amount = 0;
            // if($discount->type == "persen"){
            //     $amount = ProcessController::calculatePercentageDiscount($total, $discount->value);
            // }else{
            //     $amount = $discount->value;
            // }
            $order['voucher'] = [
                'voucher_code' => $discount->code,
                'voucher_name' => $discount->name,
                'voucher_type' => $discount->type,
                'voucher_value' => $discount->value,
                'voucher_amount' => $amount
            ];
            session()->put('order', $order);
            $cart = session()->get('cart', []);

            $totals = ProcessController::calculateTotals($cart, $username);
            $total = $totals['total'];
            return response()->json(['success' => true, 'status' => 'Voucher Applied', 'totals' => $totals, 'voucher' => $discount]);
        }else{
            return response()->json(['success' => false, 'status' => 'Voucher tidak ditemukan atau expired', 'voucher' => '']);
        }
    }
    public function removeVoucher(Request $request)
    {
        $cart = session()->get('cart', []);
        session()->forget('order.voucher');
        $order = session()->get('order', []);
        $username = $request->input('username');
        $totals = ProcessController::calculateTotals($cart, $username);

        return response()->json(['success' => true, 'status' => 'Voucher Removed', 'totals' => $totals, 'voucher' => '']);
    }
    public function checkVoucher($username)
    {
        $order = session()->get('order', []);
        $cart = session()->get('cart', []);
        $totals = ProcessController::calculateTotals($cart, $username);
        if(@$order['voucher']){
            return response()->json(['success' => true, 'status' => 'Voucher Found','totals' => $totals, 'voucher' => $order['voucher']]);
        }else{
            return response()->json(['success' => false, 'status' => 'Voucher Not Found', 'totals'=> 0, 'voucher' => '']);
        }
    }
}
