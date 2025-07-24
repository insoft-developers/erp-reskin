<?php

namespace App\Http\Controllers\StoreFront;

use App\Http\Controllers\Controller;
use App\Http\Controllers\StoreFront\ProcessController;
use Illuminate\Http\Request;
use App\Traits\DeliveryTrait;
use App\Models\Account;
use App\Models\InterProduct;
use App\Models\Material;
use App\Models\QrCode;
use App\Models\MdCustomer;
use App\Models\MlAccountInfo;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\PenjualanProductVarian;
use App\Traits\CustomerServiceTrait;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\Storefront;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    use DeliveryTrait, CustomerServiceTrait;

    public function index($username)
    {
        $footer = false;
        $view = $username . '/checkout';
        $cart = session()->get('cart', []);
        $totals = ProcessController::calculateTotals($cart, $username);
        $previousUrl = session('previous_url', url('/'));
        $order = session()->get('order', []);
        // Log::debug($totals);
        // Log::info($order);
        $store = Account::where('username', $username)->select('ml_accounts.id', 'ml_accounts.username', 'ml_account_info.payment_method', 'ml_account_info.shipping')->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')->first();
        return view('storefront.checkout', compact('cart', 'store', 'order', 'username', 'view', 'footer', 'totals', 'previousUrl'));
    }
    public function delivery($username)
    {
        $footer = false;
        $cart = session()->get('cart', []);
        $totals = ProcessController::calculateTotals($cart, $username);
        $order = session()->get('order', []);
        $store = Account::where('username', $username)->select('ml_accounts.id', 'ml_accounts.username', 'ml_account_info.payment_method', 'ml_account_info.shipping', 'ml_account_info.province_id', 'ml_account_info.city_id', 'ml_account_info.subdistrict_id')->join('ml_account_info', 'ml_accounts.id', '=', 'ml_account_info.user_id')->first();
        $provinces = $this->getData('province');
        return view('storefront.delivery', compact('cart', 'order', 'username', 'footer', 'totals', 'store', 'provinces'));
    }
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        $order = session()->get('order', []);
        $qr = session()->get('qr');

       
        $username = $request->username;
        $getTotal = ProcessController::calculateTotals($cart, $username);
        $name = isset($order['customer_name']) ? $order['customer_name'] : '';
        $phone = $order['phone_number'];
        $payment = $order['payment_type'];
        $qrTable = $order['qrTable'];
        $branchID = $order['branch_id'];
        $cust_alamat = @$order['cust_alamat'] ? $order['cust_alamat'] : '';
        $cust_provinsi = @$order['cust_provinsi'] ? $order['cust_provinsi'] : '';
        $cust_kota = @$order['cust_kota'] ? $order['cust_kota'] : '';
        $cust_kecamatan_id = @$order['cust_kecamatan_id'] ? $order['cust_kecamatan_id'] : '';
        $cust_kecamatan = @$order['cust_kecamatan'] ? $order['cust_kecamatan'] : '';
        $shipping_code = @$order['shipping_code'] ? $order['shipping_code'] : '';
        $shipping_service = @$order['shipping_service'] ? $order['shipping_service'] : '';
        $shipping_cost = @$order['shipping_cost'] ? $order['shipping_cost'] : '';
        $user = Account::where('username', $request->username)->first();
        
        $branch_id = $user->branch_id;
        $payment_info = MlAccountInfo::where('user_id', $branch_id)->first();

        $found = null;
        $owner = null;
        $rekening = null;
        if($payment_info) {
            $payment_mtd = json_decode($payment_info->payment_method, true);
            
            

            foreach ($payment_mtd as $method) {
                if ($method['method'] === 'Transfer' && isset($method['banks'])) {
                    foreach ($method['banks'] as $bank) {
                        if ($bank['remark'] === 'bank-bca') {
                            $found = $bank;
                            break 2; // keluar dari 2 level loop sekaligus
                        }
                    }
                }
            }
        }


        $cutsomerCheck = MdCustomer::where('phone', (string) $phone)->where('user_id', $user['id'])->first();
        if ($qr != '') {
            $getQr = QrCode::where('qr_id', $qr)->first();
        } else {
            $getQr = QrCode::where('id', $qrTable)->first();
        }
        if ($cutsomerCheck) {
            $custid = $cutsomerCheck->id;
        } else {
            $create = MdCustomer::create([
                'name' => $name,
                'phone' => $phone,
                'kecamatan' => '',
                'keluarahan' => '',
                'alamat' => '',
            ]);
            $custid = $create->id;
        }
        $subtotal = $getTotal['subtotal'];
        $discount = $getTotal['discount'];
        $shipping = (float) $shipping_cost;
        $tax = $getTotal['tax'];
        $paid = $getTotal['total'];
        // Create the order
        $currentDateTime = date('dm');
        $str_random = Str::upper(Str::random(2));
        $reff = 'SF' . $currentDateTime . '/' . $user->id . '/' . $str_random . rand(10, 99);
        $order = new Penjualan();
        $order->reference = $reff;
        $order->date = date('Y-m-d');
        $order->cs_id = is_null($getQr) ? null : $this->getCustomerServiceId($getQr->user_id);
        $order->customer_id = $custid;
        $order->cust_name = $name;
        $order->cust_phone = $phone;
        $order->cust_alamat = $cust_alamat;
        $order->cust_provinsi = $cust_provinsi;
        $order->cust_kota = $cust_kota;
        $order->cust_kecamatan_id = $cust_kecamatan_id;
        $order->cust_kecamatan = $cust_kecamatan;
        $order->status = '0';
        $order->payment_method = $payment;
        $order->qr_codes_id = is_null($getQr) ? null : $getQr->id;
        // $order->branch_id = is_null($getQr) ? $branchID : $getQr->branch_id;
        $order->user_id = is_null($getQr) ? $user['id'] : $getQr->user_id;
        $order->staff_id = is_null($getQr) ? $user['id'] : $getQr->user_id;
        $order->branch_id = is_null($getQr) ? $user['id'] : $getQr->user_id;
        $order->order_total = $subtotal;
        $order->diskon = $discount;
        $order->tax = $tax;
        $order->shipping = $shipping;
        $order->shipping_method = $shipping_code . ':' . $shipping_service;
        $order->paid = $paid;
        $order->save();


        $hpp_penjualan = 0;
        $detail_penjualan = null;

        foreach ($cart as $product) {
            //dd($product['variants']);
            $harga_total = (int) $product['price'] * (int) $product['quantity'];
            $item = new PenjualanProduct();
            $item->penjualan_id = $order->id;
            $item->product_id = $product['id'];
            $item->price = $product['price'];
            $item->quantity = $product['quantity'];
            $item->note = $product['notes'];
            $item->total = $harga_total;
            $item->save();

            // -Paha Ayam @2 Rp. 15,000<br>-Dada Ayam @2 Rp. 16,000<br>
            $detail_product = Product::find($product['id']);
            if($detail_product) {
                $hpp_item = $detail_product->cost * $product['quantity'];
                $hpp_penjualan = $hpp_penjualan + $hpp_item;
                $detail_penjualan .= '-'.$detail_product->name.' @'.$product['quantity'].' Rp. '.number_format($harga_total).'<br>';
            }

            // PENGURANGAN MANUFAKTURE
            if ($detail_product->created_by == 1 && $order->payment_status == 1) {
                $this->decrementStock($detail_product->id, $product['quantity']);
            }

            if (isset($product['variants'])) {
                foreach ($product['variants'] as $varianGroup => $varianItems) {
                    $note = $varianItems['notes'];
                    foreach ($varianItems['data'] as $var) {
                        $variant = new PenjualanProductVarian();
                        $variant->penjualan_product_id = $item->id;
                        $variant->varian_id = $var['id'];
                        $variant->quantity = $var['qty'];
                        $variant->price = $var['price'];
                        $variant->note = $note;
                        $variant->save();
                    }
                }
            }
        }

        Penjualan::whereId($order->id)->update([
            "detail" => $detail_penjualan,
            "hpp" => $hpp_penjualan
        ]);


        $this->customerServiceSendMessage($order->id);
        $uid = is_null($getQr) ? $user['id'] : $getQr->user_id;

        $sf = Storefront::where('user_id', $uid)->first();
        $cw = $sf->checkout_whatsapp ?? null;

        if ($cw === 1) {
            $orderd = session('order');
            $order_type = $orderd['order_type'];

            $toi = $sf->template_order_info;
            $whatsapp_number = $sf->whatsapp_number;
            $html_products = '';

            if ($order_type == 'delivery') {
                $html_products .= "*-------DETAIL PESANAN------*\n";
            } else {
                $html_products .= "*-------*DETAIL PESANAN*------*\n";
            }

            $nomor = 0;
            foreach ($cart as $ct) {
                $nomor++;
                $html_products .= '*' . $nomor . '.' . $ct['name'] . '* ' . "\n";
                $html_products .= 'Jumlah : *' . $ct['quantity'] . '* ' . "\n";


                $html_variant = '';
                $varian_price = 0;
                if (isset($ct['variants'])) {
                    foreach ($ct['variants'] as $ndex => $cv) {
                        $html_variant .= 'Varian ' . $ndex . ' : ' . "\n";
                        $html_varian_item = '';

                        foreach ($cv['data'] as $index2 => $ci) {
                            // dd($ci);
                            $varian_price = $varian_price + $ci['qty'] * $ci['price'];
                            $html_varian_item .= $ci['name'] . ' (' . $ci['qty'] . ') - (' . number_format($ci['price']) . ") \n";
                        }
                        $html_variant .= $html_varian_item;
                    }
                }
                $html_products .= $html_variant . ' ' . "\n";

                $html_products .= 'Harga Satuan: *' . number_Format($ct['price']) . '* ' . "\n";
                if (isset($ct['variants'])) {
                    $html_products .= 'Harga Varian: *' . number_Format($varian_price) . '* ' . "\n";
                }
                if (isset($ct['variants'])) {
                    $html_products .= 'Harga Total: *' . number_Format($ct['price'] * $ct['quantity'] + $varian_price) . '* ' . "\n";
                } else {
                    $html_products .= 'Harga Total: *' . number_Format($ct['price'] * $ct['quantity']) . '* ' . "\n";
                }
                $html_products .= 'Catatan: *' . $ct['notes'] . '* ' . "\n";

                $html_products .= "------------------------\n";
            }
            $html_products .= 'Subtotal: *' . number_Format($getTotal['subtotal']) . '* ' . "\n";
            $html_products .= 'Diskon: *' . number_Format($getTotal['discount']) . '* ' . "\n";
            $html_products .= 'Ongkir: *' . number_Format($getTotal['shipping']) . '* ' . "\n";
            $html_products .= 'Tax: *' . number_Format($getTotal['tax']) . '* ' . "\n";
            $html_products .= 'Total Yang Harus Dibayar: *' . number_Format($getTotal['total']) . '* ' . "\n";
            $html_products .= "------------------------\n";
            if ($order_type == 'delivery') {
                $html_products .= "*Nama:* \n";
                $html_products .= $orderd['customer_name'] . ' (' . $orderd['phone_number'] . ') ' . "\n";
                $html_products .= "*Alamat:* \n";
                $html_products .= $orderd['cust_alamat'] . "\n";
                $html_products .= "*Provinsi:* \n";
                $html_products .= $orderd['cust_provinsi'] . "\n";
                $html_products .= "*Kota:* \n";
                $html_products .= $orderd['cust_kota'] . "\n";
                $html_products .= "*Kecamatan:* \n";
                $html_products .= $orderd['cust_kecamatan'] . "\n";
                $html_products .= "*Kurir Pengiriman:* \n";
                $html_products .= $orderd['shipping_code'] . ' ' . $orderd['shipping_service'] . "\n";
            } else {
                $html_products .= "*Nama:* \n";
                $html_products .= $name . ' (' . $phone . ') ' . "\n";
                $html_products .= "*Detail Lain:* \n";
                $html_products .= is_null($getQr) ? " - \n" : $getQr['no_meja'] . "\n";
                $html_products .= "------------------------\n";
            }

            $html_products .= "*Metode Pembayaran:* \n";
            $pembayaran = '';
            if ($payment == 'kas') {
                $pembayaran = 'Tunai';
                $rekening = '';
                $owner = '';
            }
            else if($payment == 'bank-bca') {
                $pembayaran = 'Bank BCA';
                $rekening = $found['bankAccountNumber'];
                $owner = $found['bankOwner'];
            }
            else if($payment == 'bank-mandiri') {
                $pembayaran = 'Bank Mandiri';
                $rekening = $found['bankAccountNumber'];
                $owner = $found['bankOwner'];
            }
            else if($payment == 'bank-bri') {
                $pembayaran = 'Bank BRI';
                $rekening = $found['bankAccountNumber'];
                $owner = $found['bankOwner'];
            }
            else if($payment == 'bank-ni') {
                $pembayaran = 'Bank BNI';
                $rekening = $found['bankAccountNumber'];
                $owner = $found['bankOwner'];
            }
            else if($payment == 'randu-wallet') {
                $pembayaran = 'Payment Gateway';
                $rekening = '';
                $owner = '';
            }
            else if($payment == 'bank-lain') {
                $info = MlAccountInfo::where('user_id',$uid)->first();
              
                $pm = json_decode($info->payment_method, TRUE);
                $banks = $pm[2]['banks'];

                $selected = '';
                foreach ($banks as $b) {
                    if ($b['remark'] == 'bank-lain') {
                        $selected = $b['bank'];
                    }
                }

                $pembayaran = $selected;
                $rekening = $found['bankAccountNumber'];
                $owner = $found['bankOwner'];
            }
            $html_products .= $pembayaran . "\n";
            $html_products .= $owner . "\n";
            $html_products .= $rekening . "\n";
            $html_products .= "------------------------\n";

            // dd($html_products);

            $kirim_wa = str_replace("[Detail-Order]", $html_products, $toi);
            session([
                'whatsapp_number' => $whatsapp_number,
                'kirim_wa' => $kirim_wa,
                "cw" => $cw
            ]);
        }

        // Clear the cart
        session()->forget('cart');
        session()->forget('order');
        session()->forget('qr');
        if ($order) {
            // disini masukkan pesan wa
            return response()->json([
                'success' => true,
                'payment_method' => $payment,
                'orderid' => $order->id,
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function decrementStock($product_id, $quantity)
    {
        try {
            $ingredients = ProductComposition::where('product_id', $product_id)->get();
            foreach ($ingredients as $key => $ingredient) {
                $stock_use = $quantity * $ingredient->quantity;

                if ($ingredient->product_type == 2) {
                    // JIKA BAHAN SETENGAH JADI
                    $inter_product_id = $ingredient->material_id;
                    $inter_product = InterProduct::find($inter_product_id);
                    $inter_product->stock = $inter_product->stock - $stock_use;
                    $inter_product->save();
                } elseif ($ingredient->product_type == 1) {
                    // JIKA BAHAN BAKU
                    $material_id = $ingredient->material_id;
                    $material = Material::find($material_id);
                    $material->stock = $material->stock - $stock_use;
                    $material->save();
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
