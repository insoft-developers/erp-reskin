<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\PosController;
use App\Jobs\SendWhatsAppMessageGoPremium;
use App\Models\InterProduct;
use App\Models\Invoice;
use App\Models\InvoiceTermin;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MtRekapitulasiHarian;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\TransactionProduct;
use App\Models\WalletLogs;
use App\Traits\CustomerServiceTrait;
use App\Traits\MobileJournalTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuController extends Controller
{
    use MobileJournalTrait, CustomerServiceTrait;

    public function callback(Request $request)
    {
        // Log::debug($request->all());
        $merchantKey = '';
        if (env('DUITKU_SANDBOX') === true) {
            $merchantKey = env('DUITKU_MERCHANT_KEY_SANDBOX');
        } else {
            $merchantKey = env('DUITKU_MERCHANT_KEY_PRODUCTION');
        }
        $merchantCode = $request->merchantCode;
        $amount = $request->amount;
        $merchantOrderId = $request->merchantOrderId;
        $signature = $request->signature;
        $additionalParam = $request->additionalParam;
        // untuk debug callback duitku dan perlu di filter dulu $request->additionalParam sesuai kebutuhan
        // $debug_send_callback_to_wa = ['081212994496'];
        // foreach ($debug_send_callback_to_wa as $phone) {
        //     $params = [
        //         'phone' => $phone,
        //         'token' => env('WHATSAPP_API_TOKEN'),
        //         'message' => json_encode($request->all())
        //     ];

        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, "https://wa.randu.co.id/api/v1/whatsapp/send?" . http_build_query($params));
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_exec($ch);
        //     curl_close($ch);
        //     $sleepTime = rand(1, 5);
        //     sleep($sleepTime);
        // }

        if (!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature)) {
            $params = $merchantCode . $amount . $merchantOrderId . $merchantKey;
            $calcSignature = md5($params);
            DB::beginTransaction();
            try {
                if ($additionalParam === 'go_premium') {
                    // Log::debug('Log Premium | ' . json_encode($request->all()));
                    $reference = $request->reference;
                    $queryLog = DB::table('subscription_logs')->where('reference', $reference);

                    $currentLog = $queryLog->first();
                    $plan = DB::table('subscription_plans')->where('id', $currentLog->subscription_id)->first();
                    if ($plan && $currentLog) {
                        $currentTime = date("Y-m-d H:i:s");

                        $existedLog = DB::table('subscription_logs')
                            ->where('status', 1)
                            ->where('account_id', $currentLog->account_id)
                            ->orderByDesc('subscription_expiry_date')
                            ->first();

                        // jika $existedLog tidak ada, maka kita ambil dari $currentTime + plan freq (alias kita ambil dari data owner)
                        $expiryDate = date(
                            "Y-m-d H:i:s",
                            strtotime(
                                (
                                    $existedLog ?
                                    $existedLog->subscription_expiry_date :
                                    $currentTime
                                ) . (
                                    $plan->frequency === 'Yearly' ?
                                    " +1 year" :
                                    " +3 month"
                                )
                            )
                        );

                        $queryLog->update([
                            'publisherOrderId' => $request->publisherOrderId,
                            'status' => 1,
                            'subscription_expiry_date' => $expiryDate,
                        ]);

                        $currentUser = DB::table('ml_accounts')->whereId($currentLog->account_id)->first();
                        if ($currentUser) {
                            $owner = DB::table('ml_accounts')
                                ->where('email', $currentUser->email)
                                ->where('role_code', 'owner')
                                ->first();
                            if ($owner) {
                                DB::table('owner_detail_users')
                                    ->where('owner_id', $owner->id)
                                    ->update(['is_active' => 1]);
                            }
                        }

                        $plan = DB::table('subscription_plans')->whereId($currentLog->subscription_id)->first();
                        WalletLogs::create([
                            'user_id' => $currentLog->account_id,
                            'amount' => $plan->price,
                            'type' => '-',
                            'from' => 'Go Premium',
                            'group' => 'transaction-fee',
                            'note' => 'Transaksi Fee Randu Wallet - Go Premium',
                            'reference' => $reference,
                            'status' => 3,
                        ]);

                        $curr_user = DB::table('ml_accounts')->whereId($currentLog->account_id)->first();
                        // $have_owner = DB::table('ml_accounts')->whereEmail($curr_user->email)->whereRole_code('owner')->first();
                        $have_owner = DB::table('owner_detail_users')->whereUser_id($curr_user->id)->orderBy('id')->first();

                        if ($have_owner) {
                            // sudah ter duplikate untuk owner
                            // 1. update akun owner terlebih dahulu
                            DB::table('ml_accounts')->whereId($have_owner->owner_id)->update([
                                'is_upgraded' => 1,
                                'upgrade_expiry' => $expiryDate,
                            ]);

                            // // 2. Dapatkan list cabang yang terhubung dengan akun owner ini
                            $users = DB::table('owner_detail_users')->whereOwner_id($have_owner->owner_id)->pluck('user_id');
                            DB::table('ml_accounts')->whereIn('id', $users)->update([
                                'is_upgraded' => 1,
                                'upgrade_expiry' => $expiryDate,
                            ]);
                        } else {
                            // belum ter duplikate untuk owner

                            $owner_d_user = DB::table('owner_detail_users')
                                ->whereUser_id($currentLog->account_id)
                                ->where('is_active', 1)
                                ->first();

                            if ($owner_d_user) {
                                // sudah terhubung dengan owner lain
                                // ignore or skip, karena sudah terhubung dengan owner lain
                            } else {
                                // belum terhubung dengan owner lain, maka boleh di jadikan owner!

                                // 1. kita duplikate akun saat ini untuk akun owner nya
                                $role = DB::table('ml_roles')->whereCode_name('owner')->first();
                                $ac_id = DB::table('ml_accounts')->insertGetId([
                                    'email'             => $curr_user->email,
                                    'username'          => 'owner_' . $curr_user->username,
                                    'fullname'          => $curr_user->fullname,
                                    'profile_picture'   => $curr_user->profile_picture,
                                    'phone'             => $curr_user->phone,
                                    'password'          => $curr_user->password,
                                    'roles'             => $role->id,
                                    'role_code'         => $role->code_name,
                                    'status'            => $curr_user->status,
                                    'user_key'          => $this->generateRandomString(8, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
                                    'is_upgraded'       => 1,
                                    'upgrade_expiry'    => $expiryDate,
                                    'is_active'         => 1,
                                    'is_vip'            => 0,
                                    'is_soft_delete'    => 0,
                                    'recovery_code'     => 0,
                                    'created'           => $curr_user->created,
                                    'petty_cash'        => $curr_user->petty_cash,
                                    'tax'               => $curr_user->tax,
                                    'balance'           => $curr_user->balance,
                                    'created_at'        => $curr_user->created_at,
                                    'updated_at'        => $curr_user->updated_at,
                                    'referal_source'    => $curr_user->referal_source,
                                    'referal_code'      => $curr_user->referal_code,
                                    'status_cashier'    => $curr_user->status_cashier,
                                    'clock_in'          => $curr_user->clock_in,
                                    'clock_out'         => $curr_user->clock_out,
                                    'holiday'           => $curr_user->holiday,
                                    'popup_show'        => $curr_user->popup_show,
                                    'time_break'        => $curr_user->time_break,
                                ]);

                                // 2. kita samakan status is_upgraded dan upgrade_expiry mirip dengan owner nya
                                DB::table('ml_accounts')->whereId($currentLog->account_id)->update([
                                    'is_upgraded'       => 1,
                                    'upgrade_expiry'    => $expiryDate,
                                ]);

                                // 3. kita insert penyadingan otomatis antara akun owner baru dengan user saat ini
                                DB::table('owner_detail_users')->insert([
                                    'owner_id'  => $ac_id,
                                    'user_id'   => $currentLog->account_id,
                                    'is_active' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }

                        // 4. kita notif ke customer melalui pesan whatsapp
                        SendWhatsAppMessageGoPremium::dispatch($curr_user->email, $curr_user->phone, $curr_user->fullname)->onQueue('send_wa_msg_after_subscribe');;

                        DB::commit();

                        return response()->json([
                            'success' => true,
                            'message' => 'Successfully update subscription',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Subscription plan not found',
                        ], 404);
                    }
                } else if ($additionalParam === 'topup_wallet') {
                    $reference = $request->reference;
                    $queryLog = DB::table('wallet_logs')->where('reference', $reference)->where('type', '+');
                    // DB::table('wallet_logs')
                    //     ->where('reference', $reference)
                    //     ->where('type', '-')
                    //     ->where('group', 'topup-fee')
                    //     ->update(['status' => 3]);
                    WalletLogs::where('reference', $reference)->update([
                        'status' => 3 // process, auto approve by cronjob ke 3
                    ]);

                    $walletDetail = WalletLogs::where('reference', $reference)->where('type', '+')->first();
                    $currUser = DB::table('ml_accounts')->whereId($walletDetail->user_id)->first();
                    DB::table('ml_accounts')->whereId($walletDetail->user_id)->update([
                        'balance' => $currUser->balance + $walletDetail->amount,
                    ]);


                    $currentLog = $queryLog->first();
                    if ($currentLog) {
                        $currentTime = date("Y-m-d H:i:s");
                        $queryLog->update([
                            'publisherOrderId' => $request->publisherOrderId,
                            // 'status' => 1,
                            'payment_at' => $currentTime,
                        ]);

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Successfully update wallet payment',
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Wallet payment not found',
                        ], 404);
                    }
                } elseif ($additionalParam === 'pos') {
                    $reference = $request->reference;
                    $penjualan = Penjualan::where('flip_ref', $reference)->first();
                    if ($penjualan) {
                        $penjualan->update([
                            'payment_status' => 1,
                            'payment_at' => date('Y-m-d H:i:s'),
                        ]);
                        $this->customerServiceSendMessage($penjualan->id);
                        Log::info($request->all());
                        $detail_penjualans = PenjualanProduct::where('penjualan_id', $penjualan->id)->get();
                        foreach ($detail_penjualans as $key => $detail_penjualan) {
                            $product = Product::find($detail_penjualan->product_id);

                            // PENGURANGAN MANUFAKTURE
                            if ($product->created_by == 1 && $penjualan->payment_status == 1) {
                                $this->decrementStock($product->id, $detail_penjualan['quantity'], $penjualan->user_id);
                            }

                            if (isset($product) && $product->buffered_stock == 1) {
                                $this->logStock('md_product', $product->id, 0, $detail_penjualan['quantity'], $penjualan->user_id);
                                $product->quantity = $product->quantity - $detail_penjualan['quantity'];
                                $product->save();
                            }
                        }

                        $this->send_to_journal($penjualan->id, $penjualan->user_id);
                        // $updateRekapitulasiHarian = new PosController();
                        $this->updateRekapitulasiHarian($penjualan);

                        WalletLogs::where('reference', $penjualan->flip_ref)->update([
                            'status' => 2 // process, auto approve by cronjob ke 3
                        ]);

                        // $currUser = DB::table('ml_accounts')->whereId($pen->user_id)->first();
                        // DB::table('ml_accounts')->whereId($pen->user_id)->update([
                        //     'balance' => $currUser->balance + $pen->paid,
                        // ]);

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Successfully update penjualan',
                        ]);
                    }
                } elseif ($additionalParam === 'storefront') {
                    Log::info($request->all());
                    $reference = $request->reference;
                    $penjualan = Penjualan::where('flip_ref', $reference)->first();
                    if ($penjualan) {
                        $pen = $penjualan;
                        $penjualan->update([
                            'payment_status' => 1,
                            // 'status'    => 'Paid',
                            'status'    => '0', // 0 = pending
                            'payment_at' => date('Y-m-d H:i:s'),
                        ]);

                        $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
                        foreach ($detail_penjualans as $key => $detail_penjualan) {
                            $product = Product::find($detail_penjualan->product_id);

                            // PENGURANGAN MANUFAKTURE
                            if ($product->created_by == 1 && $penjualan->payment_status == 1) {
                                $this->decrementStock($product->id, $detail_penjualan['quantity'], $penjualan->user_id);
                            }

                            if (isset($product) && $product->buffered_stock == 1) {
                                $this->logStock('md_product', $product->id, 0, $detail_penjualan['quantity'], $penjualan->user_id);
                                $product->quantity = $product->quantity - $detail_penjualan['quantity'];
                                $product->save();
                            }
                        }

                        // $updateRekapitulasiHarian = new PosController();
                        $this->updateRekapitulasiHarian($penjualan);

                        // $currUser = DB::table('ml_accounts')->whereId($pen->user_id)->first();
                        // DB::table('ml_accounts')->whereId($pen->user_id)->update([
                        //     'balance' => $currUser->balance + $pen->paid,
                        // ]);
                        // DB::table('wallet_logs')->where('reference', $reference)->update(['status' => 3]);
                        DB::table('wallet_logs')->where('reference', $reference)->update(['status' => 2]); // 2 = sedang di proses, otomatis ke 3 by cronjob dan auto update saldo user sudahan

                        $this->send_to_journal($pen->id, $pen->user_id);

                        DB::commit();
                        return response()->json([
                            'success' => true,
                            'message' => 'Successfully update penjualan',
                        ]);
                    }
                } else if ($additionalParam === 'katalog-randu') {
                    TransactionProduct::where('flip_ref', $request->reference)->update([
                        'status_payment' => 1,
                    ]);
                } elseif ($additionalParam === 'invoice-generator') {
                    $invoice = Invoice::where('flip_ref', $request->reference);
                    $inv = $invoice->first();
                    $invoice->update([
                        'status' => 1,
                    ]);

                    WalletLogs::where('reference', $inv->flip_ref)->update([
                        'status' => 2 // process, auto approve by cronjob ke 3
                    ]);

                    // $currUser = DB::table('ml_accounts')->whereId($invoice->user_id)->first();
                    // DB::table('ml_accounts')->whereId($invoice->user_id)->update([
                    //     'balance' => $currUser->balance + $invoice->grand_total,
                    // ]);

                    $this->send_to_journal_invoice($inv->id, $inv->user_id);
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully update invoice',
                    ]);
                } elseif ($additionalParam === 'invoice-generator-termin') {
                    $termin = InvoiceTermin::where('flip_ref', $request->reference);
                    $inv = $termin->first();
                    $termin->update([
                        'status' => 1,
                    ]);

                    WalletLogs::where('reference', $inv->flip_ref)->update([
                        'status' => 2 // process, auto approve by cronjob ke 3
                    ]);

                    // $currUser = DB::table('ml_accounts')->whereId($invoice->user_id)->first();
                    // DB::table('ml_accounts')->whereId($invoice->user_id)->update([
                    //     'balance' => $currUser->balance + $invoice->grand_total,
                    // ]);

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Successfully update termin',
                    ]);
                } else {
                    //Other additionalParam
                }
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('duitku callback', [$e->getMessage()]);
                throw $e;
            }
        } else {
            Log::error('duitku callback', ['Bad Parameter']);
            throw new Exception('Bad Parameter');
        }
    }

    public function decrementStock($product_id, $quantity, $user_id)
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

                    $this->logStock('md_inter_product', $inter_product->id, 0, $stock_use, $user_id);
                } else if ($ingredient->product_type == 1) {
                    // JIKA BAHAN BAKU
                    $material_id = $ingredient->material_id;
                    $material = Material::find($material_id);
                    $material->stock = $material->stock - $stock_use;
                    $material->save();

                    $this->logStock('md_material', $material->id, 0, $stock_use, $user_id);
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function logStock($table, $id, $stock_in, $stock_out, $user_id)
    {
        try {
            LogStock::create([
                'user_id' => $user_id,
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
            ]);

            return true;
        } catch (\Throwable $th) {
            Log::info($th);
            return false;
        }
    }

    public function updateRekapitulasiHarian($penjualan)
    {
        try {
            $userId = $penjualan->staff_id;

            $order_total = $penjualan->paid;
            $payment_method = $penjualan->payment_method;

            $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();

            if ($payment_method == 'kas') {
                $rekapitulasiHarian['cash_sale'] = $rekapitulasiHarian['cash_sale'] + $order_total;
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] + $order_total;
            } elseif (
                $payment_method == 'bank-bca' ||
                $payment_method == 'bank-bni' ||
                $payment_method == 'bank-mandiri' ||
                $payment_method == 'bank-bri' ||
                $payment_method == 'bank-lain'
            ) {
                $rekapitulasiHarian['transfer_sales'] = $rekapitulasiHarian['transfer_sales'] + $order_total;
            } elseif ($payment_method == 'randu-wallet') {
                $rekapitulasiHarian['payment_gateway_sales'] = $rekapitulasiHarian['payment_gateway_sales'] + $order_total;
            } elseif ($payment_method == 'piutang-marketplace' || $payment_method == 'piutang-cod' || $payment_method == 'piutang-usaha') {
                $rekapitulasiHarian['piutang_sales'] = $rekapitulasiHarian['piutang_sales'] + $order_total;
            }

            $rekapitulasiHarian['total_sales'] = $rekapitulasiHarian['total_sales'] + $order_total;
            $rekapitulasiHarian->save();

            return true;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan membuka kasir terlebih dahulu',
            ]);
        }
    }
}
