<?php

namespace App\Http\Controllers\StoreFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Log;

class ProcessController extends Controller
{
    public static function getTotalQuantity($cart)
    {
        $totalQuantity = 0;
        foreach ($cart as $product) {
            $totalQuantity += (float)$product['quantity'];
        }
        return $totalQuantity;
    }

    public static function parseVariants($cart){
        $variants = "";
        foreach ($cart as $product) {
            if(isset($product['variants']))
                $variants = $product['variants'];
        }
        return $variants;
    }

    public static function calculateTotals($cart, $username)
    {
        $subtotal = 0;
        $variant = 0;
        $discount = 0;
        $tax = 0;
        $shipping = 0;
        $weight = 0;
        foreach ($cart as $product) {
            if(isset($product['variants'])){
                $var = self::calculateVariants($product['variants']);

                $variant += (float)$var * (float)$product['quantity'];
            }
            $weight +=  (float)$product['weight'] * (float)$product['quantity'];
            $subtotal += (float)$product['price'] * (float)$product['quantity'];
        }
        $shipping = self::calculateShipping();
        $subtotal = $subtotal + $variant;
        if($subtotal > 0){
            $discount = ProcessController::calculateDiscount($subtotal);
        }
        $totalBeforeTax = $subtotal - $discount;
        $userTax = Account::where('username', $username)->select('tax')->first();
        $tax = ($totalBeforeTax * (float)$userTax['tax'])/100;
        $total = $totalBeforeTax + $tax;
        $total = $total + $shipping;
        return [
            'variant' => $variant,
            'subtotal' => $subtotal,
            'weight' => $weight,
            'shipping' => $shipping,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'totalQuantity' => ProcessController::getTotalQuantity($cart)
        ];
    }
    public static function calculateVariants($variants){
        $variant = 0;
        foreach ($variants as $varGroup => $varItem){
            foreach($varItem["data"] as $var){
                $variant += (float)$var['price'];
            }
        }
        return $variant;
    }
    public static function calculateShipping()
    {
        $order = session()->get('order');
        $shipping = 0;
        if(@$order['shipping_code']){
            $shipping = (float)$order['shipping_cost'];
        }
        return $shipping;
    }
    public static function calculateDiscount($subtotal)
    {
        $order = session()->get('order');
        $discount = 0;
        if(@$order['voucher']){
            if($order['voucher']['voucher_type'] == "nominal"){
                $discount = $order['voucher']['voucher_value'];
            }else{
                $discount = self::calculatePercentageDiscount($subtotal, $order['voucher']['voucher_value']);
            }
        }
        return $discount;
    }
    public static function calculatePercentageDiscount($originalPrice, $percentageDiscount) {
        $discountAmount = ($originalPrice * $percentageDiscount) / 100;
        return $discountAmount;
    }
}
