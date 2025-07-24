<?php

namespace App\Http\Controllers\API;

use App\Events\DynamicEvent;
use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Main\PengeluaranController;
use App\Http\Controllers\ManajemenPesananController;
use App\Http\Requests\PengeluaranRequest;
use App\Models\Account;
use App\Models\BusinessGroup;
use App\Models\Discount;
use App\Models\InterProduct;
use App\Models\LogDiscountUse;
use App\Models\LogStock;
use App\Models\Material;
use App\Models\MdCustomer;
use App\Models\MdExpense;
use App\Models\MdProduct;
use App\Models\MdProductVariant;
use App\Models\MlAccount;
use App\Models\MlBank;
use App\Models\MlCurrentAsset;
use App\Models\MlIncome;
use App\Models\MtKasKecil;
use App\Models\MtPengeluaranOutlet;
use App\Models\MtRekapitulasiHarian;
use App\Models\PaymentMethodFlags;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\PenjualanProductVarian;
use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\QrCode;
use App\Models\Receivable;
use App\Models\RoCity;
use App\Models\RoDistrict;
use App\Models\RoProvince;
use App\Models\WalletLogs;
use App\Notifications\OrderNotification;
use App\Traits\CommonTrait;
use App\Traits\CustomerServiceTrait;
use App\Traits\DuitkuTrait;
use App\Traits\MobileJournalTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use phpseclib3\Crypt\RC2;
use Illuminate\Support\Str;

class PosController extends Controller
{
    use DuitkuTrait, MobileJournalTrait, CustomerServiceTrait;

    public function product(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $user = Account::where('id', $userId)->first();
        $user_id = $this->get_owner_id($userId);

        $columns = [
            'id',
            'category_id',
            'code',
            'sku',
            'barcode',
            'name',
            'price',
            'cost',
            'unit',
            'quantity',
            'stock_alert',
            'sell',
            'created',
            'user_id',
            'is_variant',
            'is_manufactured',
            'buffered_stock',
            'price_ta',
            'price_mp',
            'price_cus',
            'is_editable',
        ];

        $keyword = $request->search;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;
        $category_id = $request->category_id;
        $price_type = $request->price_type;

        $limit = limitList($per_page);

        $data = Product::orderBy('name', 'asc')
            ->select($columns)
            ->where('user_id', $user_id)
            ->where(function ($query) {
                $query->where('buffered_stock', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('buffered_stock', 1)
                            ->whereRaw('quantity > stock_alert');
                    });
            })
            // ->where(function ($result) use ($keyword, $columns) {
            //     if ($keyword != '') {
            //         $result->where($columns, 'LIKE', '%' . $keyword . '%');
            //     }
            // });
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        if ($category_id) {
            $data = $data->whereCategory_id($category_id);
        }

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        foreach ($data as $key => $value) {
            if ($price_type == 'price_ta') {
                $price = $value['price_ta'];
            } elseif ($price_type == 'price_mp') {
                $price = $value['price_mp'];
            } elseif ($price_type == 'price_cus') {
                $price = $value['price_cus'];
            } else {
                $price = $value['price'];
            }
            $value['price'] = $price;
            // $value['image'] = $value->image ? '$value->image->url' : '';
            // if ($value->image) {
            //     // $value['image'] = null;
            // } else {
            //     // $value['image'] = 'b';
            // }
            $data[$key]['category_name'] = $value->category()->first()->name ?? null;
            if ($value->is_variant === 2) {
                $data[$key]['variant_groups'] = $data[$key]->variant()->groupBy('varian_group')->pluck('varian_group');
                $data[$key]['variant'] = $data[$key]->variant()
                    ->select([
                        'id',
                        'product_id',
                        'varian_group',
                        'varian_name',
                        'sku',
                        'varian_price',
                        'single_pick',
                        'max_quantity'
                    ])->get() ?? [];
            } else {
                $data[$key]['variant_groups'] = [];
                $data[$key]['variant'] = [];
            }

            $data[$key]['qty_allowed_to_sell'] = ($data[$key]->buffered_stock == 1) ? $data[$key]->quantity - $data[$key]->stock_alert : null;
            if ($data[$key]['qty_allowed_to_sell'] < 0) {
                $data[$key]['qty_allowed_to_sell'] = 0;
            }
            $data[$key]->image_url = $value->image ? '/storage/images/product/' . $value->image->url : '';
            unset($data[$key]["image"]);
        }

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total_items' => $data->total(),
            ],
        ]);
    }

    public function productCategory(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $user = Account::where('id', $userId)->first();
        $user_id = $this->get_owner_id($userId);

        $category_products = DB::table('md_product_category')
            ->where('user_id', $user_id)
            ->where('is_Deleted', 0)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $category_products
        ]);
    }

    public function tables(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $tables = DB::table('qr_codes')->where('user_id', $userId)->get();

        return response()->json([
            'status' => true,
            'data' => $tables
        ]);
    }

    public function checkStatusCashier(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $check = MtKasKecil::where('user_id', $userId)->orderBy('id', 'desc')->first();
        $user = DB::table('ml_accounts')->whereId($userId)->first();

        if ($check) {
            $open_cashier = Carbon::parse($check->open_cashier_at)->format('Y-m-d');
            $now = now()->format('Y-m-d');

            if ($open_cashier == $now && $check->close_cashier_at == null) {
                return response()->json([
                    'status' => true,
                    'message' => 'Kasir sudah dibuka',
                    'data' => [
                        'fullname' => $user->fullname,
                        'role' => $user->role_code,
                        'branch' => $user->branch_id ? ($user->branch->name ?? null) : null,
                        'status_cashier' => 'open',
                        'open_cashier_at' => $check->open_cashier_at
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Kasir belum dibuka',
                    'data' => [
                        'fullname' => $user->fullname,
                        'role' => $user->role_code,
                        'branch' => $user->branch_id ? ($user->branch->name ?? null) : null,
                        'status_cashier' => 'close',
                        'open_cashier_at' => $check->open_cashier_at
                    ]
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Kasir belum dibuka',
                'data' => [
                    'fullname' => $user->fullname,
                    'role' => $user->role_code,
                    'branch' => $user->branch_id ? ($user->branch->name ?? null) : null,
                    'status_cashier' => 'close',
                ]
            ]);
        }
    }

    public function openCashier(Request $request)
    {
        $data = $request->all();
        $userId = Auth::user()->id ?? session('id');
        $user = MlAccount::whereId($userId)->first();

        try {
            return $this->atomic(function () use ($data, $userId, $user) {
                $check = MtKasKecil::where('user_id', $userId)->orderBy('id', 'desc')->first();
                if ($check) {
                    $open_cashier = Carbon::parse($check->open_cashier_at ?? null)->format('Y-m-d');
                    $now = now()->format('Y-m-d');

                    if ($open_cashier == $now && $check->close_cashier_at == null) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Maaf, Kasir sudah terbuka! Coba Logout lalu Login Lagi',
                        ]);
                    } else {
                        if ($open_cashier < $now) {
                            // JIKA JAM KASIR DI HARI SEBELUMNYA LUPA UNTUK MENUTUP, MAKA PERLU DI TUTUP DULU
                            $check->update([
                                'close_cashier_at' => Carbon::parse($check->open_cashier_at)->format('Y-m-d') . ' 23:59:59'
                            ]);
                        }

                        $user->status_cashier = 1;
                        $user->save();

                        $create = MtKasKecil::create([
                            'user_id' => $user->id,
                            'initial_cash_amount' => $data['initial_cash_amount'],
                            'open_cashier_at' => now()
                        ]);

                        MtRekapitulasiHarian::create([
                            'user_id' => $user->id,
                            'brach_id' => $user->branch_id ?? 0,
                            'mt_kas_kecil_id' => $create->id,
                            'initial_cash' => $data['initial_cash_amount'],
                            'total_cash' => $data['initial_cash_amount'],
                        ]);

                        return response()->json([
                            'status' => true,
                            'message' => 'Berhasil Membuka Kasir',
                            'data' => $create
                        ]);
                    }
                } else {
                    $user->status_cashier = 1;
                    $user->save();

                    $create = MtKasKecil::create([
                        'user_id' => $user->id,
                        'initial_cash_amount' => $data['initial_cash_amount'],
                        'open_cashier_at' => now()
                    ]);

                    MtRekapitulasiHarian::create([
                        'user_id' => $user->id,
                        'brach_id' => $user->branch_id ?? 0,
                        'mt_kas_kecil_id' => $create->id,
                        'initial_cash' => $data['initial_cash_amount'],
                        'total_cash' => $data['initial_cash_amount'],
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Membuka Kasir',
                        'data' => $create
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Membuka Kasir',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function closeCashier(Request $request)
    {
        $data = $request->all();
        $userId = Auth::user()->id ?? session('id');
        $user = MlAccount::whereId($userId)->first();

        try {
            return $this->atomic(function () use ($data, $user) {
                $check = MtKasKecil::where('user_id', $user->id)->orderBy('id', 'desc')->first();

                $user->status_cashier = 0;
                $user->save();

                // JIKA KASIR BELUM CLOSE DI HARI BERBEDA MAKA UPDATE DULU CLOSE CASHIR YANG LALU
                if ($check && $check->close_cashier_at == null && Carbon::parse($check->open_cashier_at)->format('Y-m-d') < now()->format('Y-m-d')) {
                    $check->update([
                        'close_cashier_at' => Carbon::parse($check->open_cashier_at)->format('Y-m-d') . ' 23:59:59'
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Tutup Kasir',
                        'data' => $check
                    ]);
                } elseif ($check && $check->close_cashier_at != null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Silahkan membuka kasir terlebih dahulu',
                    ]);
                } elseif ($check && $check->close_cashier_at == null && Carbon::parse($check->open_cashier_at)->format('Y-m-d') == now()->format('Y-m-d')) {
                    $check->update([
                        'close_cashier_at' => now(),
                    ]);

                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil Tutup Kasir',
                        'data' => $check
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Membuka Kasir',
            ]);
        }
    }

    public function discount(Request $request)
    {
        $columns = [
            'id',
            'name',
            'code',
            'type',
            'value',
            'expired_at',
            'min_order',
            'account_id',
            'max_use',
            'allowed_multiple_use',
        ];

        $keyword = $request->search;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;
        $subtotal = $request->subtotal ?? 0;

        $limit = limitList($per_page);
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));

        $user = MlAccount::find(Auth::user()->id ?? session('id'));
        $allUserId = MlAccount::where('branch_id', $user->branch_id)->pluck('id')->toArray();

        // $subtotal = $request->subtotal * 1;
        $data = Discount::orderBy('name', 'asc')
            ->select($columns)
            ->whereIn('account_id', $allUserId)
            // ->where('expired_at', '>=', now()->format('Y-m-d'))
            // ->where('min_order', '<=', $subtotal)
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        foreach ($data as $key => $value) {
            $value['discount_use'] = $value->logDiscountUse()->count() ?? 0;
            $value['discount_allowed_use'] = $value->max_use - $value->discount_use;
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function discountWeb(Request $request)
    {
        $columns = [
            'id',
            'name',
            'code',
            'type',
            'value',
            'expired_at',
            'min_order',
            'account_id',
            'max_use',
            'allowed_multiple_use',
        ];

        $keyword = $request->search;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;
        $subtotal = $request->subtotal ?? 0;

        $limit = limitList($per_page);
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));

        $user = MlAccount::find(Auth::user()->id ?? session('id'));
        $allUserId = MlAccount::where('branch_id', $user->branch_id)->pluck('id')->toArray();

        // $subtotal = $request->subtotal * 1;
        $data = Discount::orderBy('name', 'asc')
            ->select($columns)
            ->whereIn('account_id', $allUserId)
            ->where('expired_at', '>=', now()->format('Y-m-d'))
            ->where('min_order', '<=', $subtotal)
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        foreach ($data as $key => $value) {
            $value['discount_use'] = $value->logDiscountUse()->count() ?? 0;
            $value['discount_allowed_use'] = $value->max_use - $value->discount_use;
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function checkApplyDiscount(Request $request)
    {
        $customer_id = $request->customer_id;
        $discount_id = $request->discount_id;

        $discount = Discount::where('id', $discount_id)->first();
        $customer = MdCustomer::where('id', $customer_id)->first();

        if (!$customer) {
            $msg = 'Customer tidak ditemukan';
        }

        if (!$discount) {
            $msg = 'Diskon tidak ditemukan';
        }

        $discount_use = LogDiscountUse::where('discount_id', $discount_id)->where('customer_id', $customer_id)->get();
        $discount_allowed_use = ($discount_use->count() == 1 && $discount->allowed_multiple_use == 0) ? 0 : $discount->max_use - $discount_use->count();

        if ($discount_allowed_use == 0) {
            $msg = 'Anda tidak bisa menggunakan diskon ini';
        } else {
            $msg = 'Anda bisa menggunakan diskon ini';
        }

        return response()->json([
            'status' => true,
            'message' => $msg
        ]);
    }

    public function typePayment()
    {
        $userId = $this->get_branch_id(Auth::user()->id ?? session('id'));

        $info = DB::table('ml_account_info')->where('user_id', $userId)->first();


        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan setting metode pembayaran terlebih dahulu.',
                // 'data' => [
                //     [
                //         'method'=> 'Bayar Tunai',
                //         'selected'=> true,
                //         'code'=> 'kas',
                //         'description'=> 'Pembayaran Dengan Uang Tunai di Kasir'
                //     ]
                // ]
            ]);
        }

        $payment_method = json_decode($info->payment_method);
        $payment = [];
        $kasbon = [
            'id' => 4,
            'method' => 'Kasbon / Piutang',
            'description' => 'Untuk Kasbon Pembayaran Tempo (kasbon/piutang)',
            'selected' => false,
            'items' => []
        ];

        $piutangCounter = 0;
        foreach ($payment_method as $key => $value) {
            if ($value->method == 'COD' && $value->selected === 'true') {
                $piutangCounter++;
            }

            if ($value->method == 'Marketplace' && $value->selected === 'true') {
                $piutangCounter++;
            }

            if ($value->method == 'Piutang' && $value->selected === 'true') {
                $piutangCounter++;
            }
        }

        if ($piutangCounter > 0) {
            $kasbon['selected'] = true;
        }

        foreach ($payment_method as $key => $value) {
            if ($value->method == 'Cash') {
                $payment[$key]['id'] = $value->id * 1;
                $payment[$key]['method'] = 'Bayar Tunai';
                $payment[$key]['selected'] = $value->selected === 'false' ? false : true;
                $payment[$key]['code'] = 'kas';
                $payment[$key]['description'] = 'Pembayaran Dengan Uang Tunai di Kasir';
                $payment[$key]['flags'] = DB::table('payment_method_flags')->where('group', 'Cash')->where('user_id', $userId)->get();
            }
            if ($value->method == 'Online-Payment') {
                $payment[$key]['id'] = $value->id * 1;
                $payment[$key]['method'] = 'Payment Gateway';
                $payment[$key]['selected'] = $value->selected === 'false' ? false : true;
                $payment[$key]['code'] = 'randu-wallet';
                $payment[$key]['description'] = 'Gunakan fasilitas Randu Wallet';
            }

            if ($value->method == 'Transfer') {
                $payment[$key]['id'] = $value->id * 1;
                $payment[$key]['method'] = 'Transfer Rekening';
                $payment[$key]['description'] = 'Bayar via Transfer / QRIS Toko / Mesin EDC';
                $payment[$key]['selected'] = $value->selected === 'false' ? false : true;

                foreach ($value->banks as $ckey => $cvalue) {
                    $payment[$key]['items'][$ckey]['id'] = $ckey + 1;
                    if ($cvalue->bank == 'Bank BCA') {
                        $payment[$key]['items'][$ckey]['code'] = 'bank-bca';
                        $payment[$key]['items'][$ckey]['flags'] = DB::table('payment_method_flags')->where('group', 'Transfer')->where('payment_method', 'bank-bca')->where('user_id', $userId)->get();
                    } else if ($cvalue->bank == 'Bank BNI') {
                        $payment[$key]['items'][$ckey]['code'] = 'bank-bni';
                        $payment[$key]['items'][$ckey]['flags'] = DB::table('payment_method_flags')->where('group', 'Transfer')->where('payment_method', 'bank-bni')->where('user_id', $userId)->get();
                    } else if ($cvalue->bank == 'Bank Mandiri') {
                        $payment[$key]['items'][$ckey]['code'] = 'bank-mandiri';
                        $payment[$key]['items'][$ckey]['flags'] = DB::table('payment_method_flags')->where('group', 'Transfer')->where('payment_method', 'bank-mandiri')->where('user_id', $userId)->get();
                    } else if ($cvalue->bank == 'Bank BRI') {
                        $payment[$key]['items'][$ckey]['code'] = 'bank-bri';
                        $payment[$key]['items'][$ckey]['flags'] = DB::table('payment_method_flags')->where('group', 'Transfer')->where('payment_method', 'bank-bri')->where('user_id', $userId)->get();
                    } else {
                        $payment[$key]['items'][$ckey]['code'] = 'bank-lain';
                        $payment[$key]['items'][$ckey]['flags'] = DB::table('payment_method_flags')->where('group', 'Transfer')->where('payment_method', 'bank-lain')->where('user_id', $userId)->get();
                    }
                    $payment[$key]['items'][$ckey]['method'] = $cvalue->bank;
                    $payment[$key]['items'][$ckey]['selected'] = json_decode($cvalue->selected);
                    $payment[$key]['items'][$ckey]['bankOwner'] = $cvalue->bankOwner;
                    $payment[$key]['items'][$ckey]['bankAccountNumber'] = $cvalue->bankAccountNumber;
                }
            }

            if ($value->method == 'COD') {
                $value->id = 1;
                $value->code = 'piutang-cod';
                $value->selected = json_decode($value->selected);
                $value->flags = DB::table('payment_method_flags')->where('group', 'COD')->where('user_id', $userId)->get();
                array_push($kasbon['items'], $value);
            }

            if ($value->method == 'Marketplace') {
                $value->id = 2;
                $value->code = 'piutang-marketplace';
                $value->selected = json_decode($value->selected);
                $value->flags = DB::table('payment_method_flags')->where('group', 'Marketplace')->where('user_id', $userId)->get();
                array_push($kasbon['items'], $value);
            }

            if ($value->method == 'Piutang') {
                $value->id = 3;
                $value->code = 'piutang-usaha';
                $value->selected = json_decode($value->selected);
                $value->flags = DB::table('payment_method_flags')->where('group', 'Piutang')->where('user_id', $userId)->get();
                array_push($kasbon['items'], $value);
            }
        }

        array_push($payment, $kasbon);

        foreach ($payment_method as $key => $value) {
            if ($value->method == 'QRIS') {
                $payment[4]['id'] = 5;
                $payment[4]['method'] = 'Instant QRIS';
                $payment[4]['selected'] = $value->selected === 'false' ? false : true;
                $payment[4]['code'] = 'randu-wallet';
                $payment[4]['description'] = 'Pembayaran Dengan Instant QRIS';
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $payment
        ]);
    }

    public function payment(Request $request)
    {
        $data = $request->all();
        $from = !isset($request->from) ? 'W' : 'M';
        try {
            if ($request->discount_id) {
                // hanya berfungsi untuk diskon yang di ambil dari table voucher
                $this->checkApplyDiscount($request);
            }

            $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));
            $user = MlAccount::find($userId);

            $currentDateTime = date('dm');
            $str_random = Str::upper(Str::random(2));
            $refid = "P$from" . $currentDateTime . '/' . $userId . '/' . $str_random . rand(10, 99);

            $customer = null;
            if ($data['customer'] === 'Walk In Customer') {
                $customer = MdCustomer::where('id', 0)->first();
            } else {
                $customer = MdCustomer::where('user_id', $userId)->where('id', $data['customer'])->first() ?? null;
            }

            if (!$customer || $customer->id == 0) {
                $data['cust_name'] = $data['customer'];
                $data['customer_id'] = null;
            } else {
                $data['customer_id'] = $customer->id;
            }

            return $this->atomic(function () use ($data, $userId, $refid, $customer, $request, $user, $from) {
                $branch_id = $this->get_owner_id(Auth::user()->id ?? session('id'));
                $staff_id = Auth::user()->id ?? session('id');

                $dataPenjualan['reference'] = $refid;

                if ($from === 'W') {
                    $dataPenjualan['flag_id'] = $data['flag_id'] ?? null;
                } else if ($from === 'M') {
                    $dataPenjualan['flag_id'] = $data['flag'] ?? null;
                }
                $dataPenjualan['payment_amount'] = $data['payment_amount'] ?? 0;


                $dataPenjualan['customer_id'] = $data['customer_id'] ?? null;
                $dataPenjualan['paid'] = $data['paid'];

                $dataPenjualan['cust_name'] = $data['cust_name'] ?? null;
                $dataPenjualan['status'] = 0;
                $dataPenjualan['custom_date'] = $data['custom_date'] ?? now();
                $dataPenjualan['payment_status'] = ($data['payment_method'] == 'randu-wallet') ? 0 : 1;
                $dataPenjualan['payment_at'] = ($data['payment_method'] == 'randu-wallet') ? null : now();
                $dataPenjualan['payment_method'] = $data['payment_method'];
                $dataPenjualan['qr_codes_id'] = $data['qr_codes_id'] ?? null;
                $dataPenjualan['branch_id'] = $data['branch_id'] ?? $branch_id;
                $dataPenjualan['staff_id'] = $data['staff_id'] ?? $staff_id;
                $dataPenjualan['sync_status'] =  null;
                $dataPenjualan['user_id'] = $userId;
                $dataPenjualan['diskon'] = $data['diskon'];
                $dataPenjualan['shipping'] = $data['shipping'];
                $dataPenjualan['order_total'] = $data['order_total'];
                $dataPenjualan['tax'] = $data['tax'];
                $dataPenjualan['note'] = $data['note'] ?? null;
                $dataPenjualan['price_type'] = $data['price_type'] ?? 'price';
                $dataPenjualan['cs_id'] = $this->getCustomerServiceId($userId);

                $tanggal_transaksi = '';
                if ($from === 'W') {
                    $tanggal_transaksi =  $data['custom_date'] . ' ' . date('H:i:s');
                } else {
                    $tanggal_transaksi =  now();
                }

                if ($from === 'W') {
                    if ($data['custom_date']) {
                        $dataPenjualan['created'] = $data['custom_date'];
                    }
                }

                // dd($dataPenjualan);
                $penjualan = Penjualan::create($dataPenjualan);
                $penjualanId = $penjualan->id;

                $product = $data['product'];
                $itemsDetails = [
                    [
                        'name' => 'Randu - ' . $refid,
                        'price' => $data['paid'],
                        'quantity' => 1
                    ],
                ];

                $detail = '';
                foreach ($product as $key => $value) {
                    if ($value['quantity'] == 0) {
                        continue;
                    }

                    $detail_product = Product::find($value['id']);

                    // PENGURANGAN MANUFAKTURE
                    if ($detail_product->created_by == 1 && $penjualan->payment_status == 1 && $data['payment_method'] != 'randu-wallet') {
                        $this->decrementStock($detail_product->id, $value['quantity'], $tanggal_transaksi);
                    }

                    if (isset($detail_product) && $detail_product->buffered_stock == 1 && $data['payment_method'] != 'randu-wallet') {
                        $detail_product->quantity = $detail_product->quantity - $value['quantity'];
                        $detail_product->save();



                        $this->logStock('md_product', $detail_product->id, 0, $value['quantity'], $tanggal_transaksi);
                    }

                    $price = $value['price'] ?? $detail_product->price;
                    $total = $detail_product->price * $value['quantity'];

                    $penjualan_product = PenjualanProduct::create([
                        'penjualan_id' => $penjualanId,
                        'product_id' => $detail_product->id,
                        'price' => $price,
                        'quantity' => $value['quantity'],
                        'total' => $total,
                        'created' => ($from === 'W' && $data['custom_date']) ? $data['custom_date'] : now(),
                        'note' => $value['note'] ?? null,
                    ]);

                    $detail .= '-' . $detail_product->name . ' @' . $value['quantity'] . ' Rp. ' . number_format($price) . '<br>';

                    if (isset($value['variant'])) {
                        foreach ($value['variant'] as $key => $varian) {
                            $detail_varian = MdProductVariant::find($varian['id']);
                            PenjualanProductVarian::create([
                                'penjualan_product_id' => $penjualan_product->id,
                                'varian_id' => $varian['id'],
                                'varian_name' => $detail_varian->varian_name,
                                'quantity' => $varian['quantity'],
                                'price' => $varian['varian_price'],
                                'note' => $varian['note'],
                            ]);
                        }
                    }

                    // ini di komen dulu aja, next pakai ini, cuman harus di siapkan harga ongkir dan pajak harus include
                    // array_push($itemsDetails, [
                    //     'name' => $detail_product->name,
                    //     'quantity' => $value['quantity'],
                    //     'price' => $price,
                    //     'total' => $total
                    // ]);
                }

                $penjualan->detail = $detail;
                $penjualan->save();


                Penjualan::whereId($penjualanId)->update([
                    'hpp' => $this->getHpp($penjualanId)
                ]);

                if ($data['payment_method'] != 'randu-wallet') {
                    // kebutuhan untuk mengirim pesan wa ke customer menggunakan layanan customer service
                    $this->customerServiceSendMessage($penjualanId);
                    $this->createPiutang($penjualan, $customer);
                }

                if ($data['payment_method'] != 'randu-wallet') {
                    $cust_name = $data['cust_name'] ?? $customer->name;
                    event(new DynamicEvent('order-channel', 'e1', [
                        'refid' => Auth::user()->id ?? session('id'),
                        'data' => [
                            'message' => 'Penjualan baru dari ' . ($cust_name) . ' sebesar Rp.' . number_format($data['order_total']),
                        ]
                    ]));
                }

                if ($request->discount_id) {
                    LogDiscountUse::create([
                        'discount_id' => $data['discount_id'],
                        'customer_id' => $customer->id ?? null,
                        'user_id' => $userId,
                        'penjualan_id' => $penjualanId,
                        'total_amount' => $penjualan->order_total,
                        'discount_amount' => $penjualan->diskon,
                    ]);
                }

                if (isset($user->petty_cash) && $user->petty_cash == 1 && $penjualan->payment_method != 'randu-wallet') {
                    $this->updateRekapitulasiHarian($penjualan);
                }

                if ($data['payment_method'] == 'randu-wallet') {
                    if ($customer->id) {
                        $customerDetail = MdCustomer::whereId($customer->id)->first();
                        if ($customerDetail) {
                            $customerDetail = [
                                'email' => $customerDetail->email ?? '',
                                'phone' => $customerDetail->phone ?? '',
                                'username' => '', // customer tidak ada username
                                'fullname' => $customerDetail->name,
                            ];
                        } else {
                            // return gagal data customer tidak ada
                            return response()->json([
                                'status' => false,
                                'message' => 'Data customer tidak ditemukan',
                            ]);
                        }
                    } else {
                        if ($customer->name && $customer->phone) {
                            $customerDetail = [
                                'email' => $customer->email ?? '',
                                'phone' => $customer->phone ?? '',
                                'username' => '', // customer tidak ada username
                                'fullname' => $request->customer,
                            ];
                        } else {
                            // return gagal data customer tidak ada
                            return response()->json([
                                'status' => false,
                                'message' => 'Data customer seperti nama dan phone wajib ada',
                            ]);
                        }
                    }

                    $return_url = '';
                    if ($data['return_url']) {
                        $return_url = $data['return_url'] . '?reference=' . $refid;
                    }
                    if (!$request->instant_qris) {
                        $invoice = $this->createInvoice('penjualan-' . $refid, $itemsDetails, ($data['return_url'] ?? ''), 'pos', $customerDetail);
                    } else {
                        $invoice = $this->createQris('penjualan-' . $refid, $itemsDetails, ($data['return_url'] ?? ''), 'pos', $customerDetail);
                    }

                    $result = $invoice['result'];
                    if ($invoice['httpCode'] === 200) {
                        $config = DB::table('ml_site_config')->first();
                        WalletLogs::create([
                            'user_id' => $userId,
                            'amount' => ($data['paid'] * $config->fee_payment_gateway) / 100,
                            'type' => '-',
                            'from' => 'POS',
                            'group' => 'transaction-fee',
                            'note' => 'Transaksi Fee Randu Wallet - POS',
                            'reference' => $result->reference,
                            'status' => '0',
                        ]);
                        WalletLogs::create([
                            'user_id' => $userId,
                            'amount' => $data['paid'],
                            'type' => '+',
                            'from' => 'POS',
                            'group' => 'income-pos',
                            'note' => 'Pemasukan POS Sebesar Rp. ' . number_format($penjualan->order_total, 0, ',', '.'),
                            'reference' => $result->reference,
                            'status' => '0',
                            'payment_return_url' => $result->paymentUrl,
                        ]);

                        $penjualanData = [
                            'payment_return_url' => $result->paymentUrl,
                            'payment_start_at' => now()
                        ];

                        if ($request->instant_qris) {
                            $penjualanData['qris_code'] = $result->qrString;
                            $penjualanData['qris_expired_at'] = now()->addMinutes(10);
                        }

                        DB::table('penjualan')->whereId($penjualanId)->update($penjualanData);
                        // UPDATE REFERENCE FROM DUITKU
                        $penjualan->flip_ref = $result->reference;
                        $penjualan->save();
                        $result->payment_method = $data['payment_method'];

                        if ($request->instant_qris) {
                            $result->paymentUrl = '';
                        }

                        return response()->json([
                            'status' => true,
                            'message' => 'Berhasil membuat penjualan',
                            'data' => $result,
                            'returnUrl' => $return_url
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Terdapat kesalahan saat mencoba melakukan pembayaran, coba lagi nanti.',
                        ], 500);
                    }
                } else {
                    $this->send_to_journal($penjualanId, $user->id);

                    return response()->json([
                        'status' => true,
                        'message' => 'Berhasil membuat penjualan',
                        'data' => $penjualan
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat penjualan',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function paymentCheck(Request $request)
    {
        $check = DB::table('penjualan')->whereReference($request->reference)->first();
        if ($check) {
            if ($check->payment_status === 1) {
                return response()->json([
                    'status' => true,
                    'message' => 'Pembayaran sudah dikonfirmasi',
                    'data' => $check,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Pembayaran belum dikonfirmasi',
                    'data' => $check,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Penjualan dengan referensi ' . $request->reference . 'tidak ditemukan',
            ], 404);
        }
    }

    public function instantQris(Request $request)
    {
        $merchantCode = env('DUITKU_MERCHANT_CODE'); // dari duitku
        $apiKey = env('DUITKU_MERCHANT_KEY_SANDBOX'); // dari duitku
        $paymentAmount = 40000;
        $paymentMethod = 'SP'; // VC = Credit Card
        $merchantOrderId = time() . ''; // dari merchant, unik
        $productDetails = 'Tes pembayaran menggunakan Duitku';
        $email = 'test@test.com'; // email pelanggan anda
        $phoneNumber = '08123456789'; // nomor telepon pelanggan anda (opsional)
        $additionalParam = ''; // opsional
        $merchantUserInfo = ''; // opsional
        $customerVaName = 'John Doe'; // tampilan nama pada tampilan konfirmasi bank
        $callbackUrl = 'https://afif.randu.co.id/api/callback-duitku-dev'; // url untuk callback
        $returnUrl = 'https://afif.randu.co.id'; // url untuk redirect
        $expiryPeriod = 10; // atur waktu kadaluarsa dalam hitungan menit
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

        // Customer Detail
        $firstName = "John";
        $lastName = "Doe";

        // Address
        $alamat = "Jl. Kembangan Raya";
        $city = "Jakarta";
        $postalCode = "11530";
        $countryCode = "ID";

        $address = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => $alamat,
            'city' => $city,
            'postalCode' => $postalCode,
            'phone' => $phoneNumber,
            'countryCode' => $countryCode
        );

        $customerDetail = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address
        );

        $item1 = array(
            'name' => 'Test Item 1',
            'price' => 10000,
            'quantity' => 1
        );

        $item2 = array(
            'name' => 'Test Item 2',
            'price' => 30000,
            'quantity' => 3
        );

        $itemDetails = array(
            $item1,
            $item2
        );

        $params = array(
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'additionalParam' => 'pos-using-qris',
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'additionalParam' => $additionalParam,
            'merchantUserInfo' => $merchantUserInfo,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            //'accountLink' => $accountLink,
            //'creditCardDetail' => $creditCardDetail,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod
        );

        $params_string = json_encode($params);
        //echo $params_string;
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
        // $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params_string)
            )
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //execute post
        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            $result = json_decode($request, true);
            return response()->json([
                'status' => true,
                'data' => $result,
            ]);
        } else {
            $request = json_decode($request);
            return response()->json([
                'status' => false,
                'message' => "Server Error " . $httpCode . " " . $request->Message,
            ], $httpCode);
        }
    }

    public function paymentValidation(Request $request)
    {
        $data = $request->all();

        if ($request->discount_id) {
            $this->checkApplyDiscount($request);
        }

        $userId = Auth::user()->id ?? session('id');
        $user = MlAccount::find($userId);

        $currentDateTime = date('YmdHis');
        $refid = 'AK' . $currentDateTime . '-' . rand(1, 1000000);
        $customer = MdCustomer::find($data['customer']) ?? null;

        if (!$customer || $customer->id == 0) {
            $data['cust_name'] = $data['customer'];
            $data['customer_id'] = null;
        } else {
            $data['customer_id'] = $customer->id;
        }

        $dataPenjualan['customer_id'] = $data['customer_id'] ?? null;
        $dataPenjualan['paid'] = $data['paid'];
        $dataPenjualan['cust_name'] = $data['cust_name'] ?? null;
        $dataPenjualan['status'] = 'Process';

        $dataPenjualan['payment_status'] = ($data['payment_method'] == 'randu-wallet') ? 0 : 1;
        $dataPenjualan['payment_at'] = ($data['payment_method'] == 'randu-wallet') ? null : now();
        $dataPenjualan['payment_method'] = $data['payment_method'];
        $dataPenjualan['qr_codes_id'] = $data['qr_codes_id'] ?? null;
        $dataPenjualan['branch_id'] = $data['branch_id'] ?? null;
        $dataPenjualan['staff_id'] = $data['staff_id'] ?? null;
        $dataPenjualan['sync_status'] =  null;
        $dataPenjualan['user_id'] = $userId;
        $dataPenjualan['diskon'] = $data['diskon'];
        $dataPenjualan['shipping'] = $data['shipping'];
        $dataPenjualan['order_total'] = $data['order_total'];
        $dataPenjualan['tax'] = $data['tax'];
        $dataPenjualan['note'] = $data['note'] ?? null;

        $product = $data['product'];
        $detail = '';
        $error = [];

        foreach ($product as $key => $value) {
            $detail_product = Product::find($value['id']);

            if (isset($detail_product) && $detail_product->buffered_stock == 1) {
                $qty_allowed_to_sell = $detail_product->quantity - $detail_product->stock_alert;
                if ($qty_allowed_to_sell < $value['quantity']) {
                    $error[] = 'Stok ' . $key + 1 . $detail_product->name . ' tidak mencukupi';
                }
            }

            $price = $detail_product->price;
            $total = $detail_product->price * $value['quantity'];

            $penjualan_product = [
                'product_id' => $detail_product->id,
                'price' => $price,
                'quantity' => $value['quantity'],
                'total' => $total,
                'created' => now(),
            ];

            $detail .= '-' . $detail_product->name . ' @' . $value['quantity'] . ' Rp. ' . number_format($price) . '<br>';

            if (isset($value['variant'])) {
                foreach ($value['variant'] as $key => $varian) {
                    $penjualan_product_varian = [
                        'varian_id' => $varian['id'],
                        'quantity' => $varian['quantity'],
                        'price' => $varian['varian_price'],
                        'note' => $varian['note'],
                    ];
                }
            }

            // ini di komen dulu aja, next pakai ini, cuman harus di siapkan harga ongkir dan pajak harus include
            // array_push($itemsDetails, [
            //     'name' => $detail_product->name,
            //     'quantity' => $value['quantity'],
            //     'price' => $price,
            //     'total' => $total
            // ]);
        }

        if (count($error) > 0) {
            return response()->json([
                'status' => false,
                'message' => $error
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Penjualan Bisa Dibuat',
            'data' => $data,
        ]);
    }

    public function customerList(Request $request)
    {
        $columns = [
            'name',
            'phone',
        ];

        $responData = [
            'id',
            'name',
        ];

        $keyword = $request->search;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;

        $limit = limitList($per_page);
        $userId = Auth::user()->id ?? session('id');
        $userId = $this->get_owner_id($userId);
        $data = MdCustomer::orderBy('name', 'asc')
            ->where('user_id', $userId)
            ->select($responData)
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function customerCreate(Request $request)
    {
        $data = $request->all();

        try {
            $userId = Auth::user()->id ?? session('id');
            $ownerId = $this->get_owner_id($userId);

            return $this->atomic(function () use ($data, $userId, $ownerId) {
                $customer = MdCustomer::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'] ?? null,
                    'province_id' => $data['province_id'] ?? null,
                    'city_id' => $data['city_id'] ?? null,
                    'district_id' => $data['district_id'] ?? null,
                    'alamat' => $data['address'] ?? null,
                    'user_id' => $ownerId
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data customer baru berhasil disimpan',
                    'data' => $customer
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal membuat customer baru',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    public function updateRekapitulasiHarian($penjualan)
    {
        try {
            // $userId = $penjualan->user_id;
            $userId = Auth::user()->id ?? session('id');

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

    public function revertRekapitulasiHarian($penjualan)
    {
        try {
            // $userId = $penjualan->user_id;
            $userId = Auth::user()->id ?? session('id');

            $order_total = $penjualan->paid;
            $payment_method = $penjualan->payment_method;

            $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();

            if ($payment_method == 'kas') {
                $rekapitulasiHarian['cash_sale'] = $rekapitulasiHarian['cash_sale'] - $order_total;
                $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] - $order_total;
            } elseif (
                $payment_method == 'bank-bca' ||
                $payment_method == 'bank-bni' ||
                $payment_method == 'bank-mandiri' ||
                $payment_method == 'bank-bri' ||
                $payment_method == 'bank-lain'
            ) {
                $rekapitulasiHarian['transfer_sales'] = $rekapitulasiHarian['transfer_sales'] - $order_total;
            } elseif ($payment_method == 'randu-wallet') {
                $rekapitulasiHarian['payment_gateway_sales'] = $rekapitulasiHarian['payment_gateway_sales'] - $order_total;
            } elseif ($payment_method == 'piutang-marketplace' || $payment_method == 'piutang-cod' || $payment_method == 'piutang-usaha') {
                $rekapitulasiHarian['piutang_sales'] = $rekapitulasiHarian['piutang_sales'] - $order_total;
            }

            $rekapitulasiHarian['total_sales'] = $rekapitulasiHarian['total_sales'] - $order_total;
            $rekapitulasiHarian->save();

            return true;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Silahkan membuka kasir terlebih dahulu',
            ]);
        }
    }

    public function managePesanan(Request $request)
    {
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));
        $data = Penjualan::query()->orderBy('id', 'desc')->where('user_id', $userId);

        if ($request->has('selected_range')) {
            $selectedRange = $request->input('selected_range');
            $today = Carbon::today();
            $startDate = $request->input('startDate') . ' 00:00:00';
            $endDate = $request->input('endDate') . ' 23:59:59';

            switch ($selectedRange) {
                case 'isToday':
                    $data->whereDate('created', $today);
                    break;
                case 'isYesterday':
                    $data->whereDate('created', $today->subDay());
                    break;
                case 'isThisWeek':
                    $data->whereBetween('created', [$today->startOfWeek(), $today->endOfWeek()]);
                    break;
                case 'isLastWeek':
                    $data->whereBetween('created', [$today->subWeek()->startOfWeek(), $today->subWeek()->endOfWeek()]);
                    break;
                case 'isThisMonth':
                    $data->whereMonth('created', $today->month)->whereYear('created', $today->year);
                    break;
                case 'isLastMonth':
                    $data->whereMonth('created', $today->subMonth()->month)->whereYear('created', $today->subMonth()->year);
                    break;
                case 'isThisYear':
                    $data->whereYear('created', $today->year);
                    break;
                case 'isLastYear':
                    $data->whereYear('created', $today->subYear()->year);
                    break;
                case 'isRangeDate':
                    $data->whereBetween('created', [$startDate, $endDate]);
                    break;
            }
        }

        if (isset($request->keyword)) {
            $keyword = $request->keyword;
            $data->where(function ($data) use ($keyword) {
                $data->where('reference', 'like', '%' . $keyword . '%')
                    ->orWhere('cust_name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customer', function ($data) use ($keyword) {
                        $data->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhere('status', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($data) use ($keyword) {
                        $data->where('payment_status', '<', 0)->whereRaw("IF(payment_status < 0, 'Canceled', IF(payment_status = 1, 'Paid', 'UnPaid')) like ?", ['%' . $keyword . '%']);
                    });
            });
        }

        // Filter by branch
        if ($request->has('branch') && $request->branch !== '') {
            $branchId = $request->branch;
            $data->where('branch_id', $branchId);
        }

        // Filter by transaction_status
        if ($request->has('transaction_status') && $request->transaction_status !== '') {
            $transactionStatus = $request->transaction_status;
            $data->where('status', $transactionStatus);
        }

        // Filter by payment_status
        if ($request->has('payment_status') && $request->payment_status !== '') {
            $paymentStatus = $request->payment_status;
            $data->where('payment_status', $paymentStatus);
        }

        // Filter by payment_method
        if ($request->has('payment_method') && $request->payment_method !== '') {
            $paymentMethod = $request->payment_method;
            $data->where('payment_method', $paymentMethod);
        }

        $data = $data->paginate(10);

        foreach ($data as $key => $value) {
            $value['cust_name'] = $value->customer()->first()->name ?? $value->cust_name;
            $value['cust_phone'] = $value->customer()->first()->phone ?? $value->cust_phone;
            $value['staff_name'] = $value->staff()->first()->fullname ?? '-';

            $value['products'] = $value->products()->with('variant')->get();
            $value['table'] = $value->desk->no_meja ?? '-';
            $value['status'] = (int)$value->status;

            foreach ($value['products'] as $key => $product) {
                $product['name'] = $product->product()->first()->name ?? '';
                $product['price_ta'] = $product->product()->first()->price_ta ?? 0;
                $product['price_mp'] = $product->product()->first()->price_mp ?? 0;
                $product['price_cus'] = $product->product()->first()->price_cus ?? 0;

                foreach ($product->variant as $key => $variant) {
                    $variant['varian_name'] = $variant->variant()->first()->varian_name ?? '';
                    $variant['varian_price'] = $variant->variant()->first()->varian_price ?? '';
                    $variant['quantity'] = (int)$variant->quantity ?? 0;
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function updateStatusPenjualan(Request $request, $id)
    {
        try {
            $status = $request->status;
            $update = Penjualan::find($id);
            $update->update(['status' => $status]);

            return response()->json([
                'status' => true,
                'message' => 'Status Penjualan Berhasil Diubah.',
                'data' => $update
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateStatusPembayaran(Request $request, $id)
    {
        try {
            $status = $request->status;
            $update = Penjualan::find($id);
            if (isset($update->staff_id) && $update->staff_id != Auth::user()->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund Gagal, Hanya Bisa Dilakukan Oleh User yang bersangkutan.',
                ]);
            }
            if ($update->payment_status != -2 && $status == -2) {
                $newRequest = new Request();
                $newRequest->replace(['id' => $id]);

                $managePesanan = new ManajemenPesananController;
                $managePesanan->single_void($newRequest);

                return response()->json([
                    'status' => true,
                    'message' => 'Send to void success.',
                    'data' => $update,
                ]);
            } else if ($status == -2) {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund Gagal, Penjualan Sudah Dibatalkan.',
                ]);
            }
            if ($update->payment_method != 'randu-wallet') {
                $update->update(['payment_status' => $status]);
            }

            $pen = Penjualan::find($id);
            $detail_penjualan = PenjualanProduct::where('penjualan_id', $pen->id)->get();
            foreach ($detail_penjualan as $key => $detail_penjualan) {
                // PENGURANGAN MANUFAKTURE
                $detail_product = Product::find($detail_penjualan->product_id);
                if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
                    $tanggal_transaksi = $pen->custom_date . ' ' . date('H:i:s', strtotime($pen->created_at));
                    $this->decrementStock($detail_product->id, $detail_product->quantity, $tanggal_transaksi);
                }
            }

            // if ($pen->payment_method == 'randu-wallet') {
            //     $status = false;
            // } else {
            //     if ($pen->payment_status != 1) {
            //         $this->revertRekapitulasiHarian($pen);

            //         $jurnalRefundController = new ManajemenPesananController;
            //         $jurnalRefundController->make_refund_journal($pen->id);
            //         // $this->reverseQuantity($pen->id);
            //     } else {
            //         $this->addRekapitulasiHarian($pen);
            //     }
            // }

            if ($pen->payment_method == 'randu-wallet') {
                $status = false;
            } else {
                if ($pen->payment_status != 1) {
                    $this->revertRekapitulasiHarian($pen);

                    $this->reverseQuantity($pen->id);
                    $jurnalRefundController = new ManajemenPesananController;
                    $jurnalRefundController->make_refund_journal($pen->id);
                } else {
                    $this->addRekapitulasiHarian($pen);

                    // indra
                    $pen = Penjualan::find($pen->id);

                    $detail_penjualans = PenjualanProduct::where('penjualan_id', $pen->id)->get();
                    foreach ($detail_penjualans as $key => $detail_penjualan) {
                        // PENGURANGAN MANUFAKTURE
                        $detail_product = Product::find($detail_penjualan->product_id);
                        if ($detail_product->created_by == 1 && $pen->payment_status == 1) {
                            $this->decrementStock($detail_product->id, $detail_penjualan->quantity, $this->get_owner_id(Auth::user()->id ?? session('id')));
                        } elseif ($detail_product->created_by != 1 && $pen->payment_status == 1  && $detail_product->buffered_stock == 1) {
                            $this->logStock('md_product', $detail_product->id, 0, $detail_penjualan->quantity, $this->get_owner_id(Auth::user()->id ?? session('id')));
                            $stock_sekarang = $detail_product->quantity;
                            $stock_akhir = $stock_sekarang - $detail_penjualan->quantity;
                            $dp = Product::findorFail($detail_product->id);
                            $dp->quantity = $stock_akhir;
                            $dp->save();
                        }
                    }
                }
            }

            // $this->updateRekapitulasiHarian($update);
            $this->send_to_journal($id, $update->user_id);

            // UPDATE PIUTANG
            if ($pen->payment_method == 'piutang-cod' || $pen->payment_method == 'piutang-usaha' || $pen->payment_method == 'piutang-marketplace') {
                $updatePiutang = new ManajemenPesananController;
                $updatePiutang->updatePiutang($pen);
            }

            return response()->json([
                'status' => true,
                'message' => 'Status Penjualan Berhasil Diubah.',
                'data' => $update
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function reverseQuantity($id)
    {
        $userId = Auth::user()->id ?? session('id');
        $penjualan_product = PenjualanProduct::where('penjualan_id', $id)->get();
        foreach ($penjualan_product as $key => $value) {
            $product = MdProduct::find($value['product_id']);

            // JIKA TIDAK MENGGUNAKAN STOCK
            if ($product->buffered_stock == 1) {
                if (isset($product)) {
                    $product['quantity'] = $product['quantity'] + $value['quantity'];
                    $product->save();
                }
                $log_stock_product = LogStock::where('relation_id', $product->id)->where('table_relation', 'md_product')->where('user_id', $userId)->where('stock_out', $value['quantity'])->orderBy('id', 'desc')->first();
                if ($log_stock_product) {
                    $log_stock_product->delete();
                }
            }

            $ingredients = ProductComposition::where('product_id', $value['product_id'])->get();

            if ($product->created_by == 1) {
                foreach ($ingredients as $key => $ingredient) {
                    $stock_use = $value->quantity * $ingredient->quantity;

                    if ($ingredient->product_type == 2) {
                        // JIKA BAHAN SETENGAH JADI
                        $inter_product_id = $ingredient->material_id;
                        $inter_product = InterProduct::find($inter_product_id);
                        $inter_product->stock = $inter_product->stock - $stock_use;
                        $inter_product->save();

                        $log_stock_inter_product = LogStock::where('relation_id', $inter_product->id)->where('table_relation', 'md_inter_product')->where('user_id', $userId)->where('stock_out', $stock_use)->orderBy('id', 'desc')->first();
                        if (isset($log_stock_inter_product)) {
                            $log_stock_inter_product->delete();
                        }
                    } else if ($ingredient->product_type == 1) {
                        // JIKA BAHAN BAKU
                        $material_id = $ingredient->material_id;
                        $material = Material::find($material_id);
                        $material->stock = $material->stock - $stock_use;
                        $material->save();

                        $log_stock_material = LogStock::where('relation_id', $material->id)->where('table_relation', 'md_material')->where('user_id', $userId)->where('stock_out', $stock_use)->orderBy('id', 'desc')->first();
                        if (isset($log_stock_material)) {
                            $log_stock_material->delete();
                        }
                    }
                }
            }
        }
    }

    public function addRekapitulasiHarian($penjualan)
    {
        try {
            $userId = Auth::user()->id ?? session('id');

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

    public function rekapitulasiHarian(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $ownerId = $this->get_owner_id($userId);

        try {
            $columns = [
                'id',
                'user_id',
                'brach_id',
                'mt_kas_kecil_id',
                'initial_cash',
                'cash_sale',
                'transfer_sales',
                'payment_gateway_sales',
                'piutang_sales',
                'outlet_output',
                'total_cash',
                'total_sales',
                'created_at',
            ];

            $keyword = $request->search;
            $per_page = $request->per_page ?? 10;
            $all = $request->all;

            $limit = limitList($per_page);

            $date = $request->date ?? date('Y-m-d');

            $data = MtRekapitulasiHarian::orderBy('id', 'desc')
                ->select($columns)
                ->where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->where(function ($result) use ($keyword, $columns) {
                    foreach ($columns as $column) {
                        if ($keyword != '') {
                            $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                        }
                    }
                });


            $data = ($all == true) ? $data->get() : $data->paginate($limit);

            foreach ($data as $key => $value) {
                $branch_name = BusinessGroup::where('user_id', $ownerId)->first();
                $value['nama_toko'] = $value->user()->first()->business_group->branch_name ?? $branch_name->branch_name;
                $value['nama_kasir'] = $value->user()->first()->fullname ?? null;
                $value['open_cashier_at'] = (isset($value->kasKecil->open_cashier_at)) ? Carbon::parse($value->kasKecil()->first()->open_cashier_at)->format('Y-m-d H:i') : null;
                $value['close_cashier_at'] = (isset($value->kasKecil->close_cashier_at)) ? Carbon::parse($value->kasKecil()->first()->close_cashier_at)->format('Y-m-d H:i') : null;
            }

            return response()->json([
                'status' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function qrCodeMeja(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $user = Account::where('id', $userId)->first();
        $user_id = $this->get_owner_id($userId);

        $keyword = $request->search ?? $request->q;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;

        $limit = limitList($per_page);

        $data = QrCode::orderBy('id', 'desc')
            ->where('user_id', $user_id);
        // if ($user->role_code == 'staff') {
        //     $data->where('branch_id', $user->branch_id);
        // }

        if ($keyword != '') {
            $data->where(function ($result) use ($keyword) {
                $result->where('no_meja', 'LIKE', '%' . $keyword . '%');
            });
        }
        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        foreach ($data as $key => $value) {
            $value['branch_name'] = $value->branch()->first()->name ?? '-';
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function updateStatusQrCodeMeja(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        try {
            $query = QrCode::query();
            if (is_array($ids)) {
                $query->whereIn('id', $ids);
            } else {
                $query->where('id', $ids);
            }
            $update = $query->update(["availability" => $status]);

            return response()->json([
                'status' => true,
                'message' => 'Status Qr Code Meja Berhasil Diubah.',
                'data' => $query->get()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function listPengeluaran(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $checkUser = MlAccount::find($userId);
        if ($checkUser->branch_id == $userId) {
            $user_id = MlAccount::where('branch_id', $checkUser->branch_id)->pluck('id');
        } else {
            $user_id = [$userId];
        }
        $columns = ['id', 'user_id', 'name', 'amount', 'sync_status', 'created_at'];

        $keyword = $request->keyword;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $keyword = $request->search;
        $per_page = $request->per_page ?? 10;
        $all = $request->all;

        $limit = limitList($per_page);

        $data = MtPengeluaranOutlet::orderBy('id', 'desc')
            ->whereIn('user_id', $user_id)
            ->select($columns)
            ->where(function ($query) use ($keyword, $columns) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })
            ->when($bulan, function ($q) use ($bulan) {
                $q->whereMonth('created_at', $bulan);
            })
            ->when($tahun, function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun);
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function createpengeluaran(PengeluaranRequest $request)
    {
        $data = $request->all();

        // try {
        return $this->atomic(function () use ($data, $request) {
            // $data['user_id'] = $this->get_owner_id(Auth::user()->id ?? session('id'));
            $data['user_id'] = Auth::user()->id ?? session('id');

            $create = MtPengeluaranOutlet::create($data);

            $pengeluaranController = new PengeluaranController();
            $pengeluaranFunction = $pengeluaranController->live_sync($create->id);

            $this->updateRekapitulasiHarianPengeluaran($request);

            return response()->json([
                'status' => true,
                'message' => 'Data Berhasil di Tambahkan!',
            ]);
        });
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Data Gagal di Tambahkan!',
        //     ]);
        // }
    }

    public function updateRekapitulasiHarianPengeluaran(Request $request)
    {
        $userId = Auth::user()->id ?? session('id');
        $amount = $request->amount;

        $rekapitulasiHarian = MtRekapitulasiHarian::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();
        $rekapitulasiHarian['total_cash'] = $rekapitulasiHarian['total_cash'] - $amount;
        $rekapitulasiHarian['outlet_output'] = $rekapitulasiHarian['outlet_output'] + $amount;
        $rekapitulasiHarian->save();

        return true;
    }

    public function errorStatus()
    {
        return response()->json([
            'status' => false,
            'message' => 'Error, Please try again!',
        ], 200);
    }

    public function detailPenjualan(Request $request)
    {
        $refid = $request->refid;
        $userId = $this->get_owner_id(Auth::user()->id ?? session('id'));

        $column = [
            'reference',
            'customer_id',
            'paid',
            'cust_name',
            'status',
            'payment_status',
            'payment_at',
            'payment_method',
            'user_id',
            'diskon',
            'shipping',
            'order_total',
            'tax',
            'note',
            'updated_at',
            'created_at',
            'id',
            'detail',
            'flip_ref'
        ];

        $data = Penjualan::select($column)->where('user_id', $userId)->where('reference', $refid)->orWhere('flip_ref', $refid)->first();
        $data['products'] = $data->products()->with('variant')->get();
        $data['status'] = (int)$data['status'];

        if ($refid && $data != null) {
            return response()->json([
                'status' => true,
                'message' => 'Data retrieved successfully.',
                'data' => $data
            ]);
        } else {
            return $this->errorStatus();
        }
    }

    public function orderDetail(Request $request)
    {
        $reference = $request->reference;
        $data = Penjualan::with(['flag', 'products.variant.variant', 'products.product'])->where('reference', $reference)->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Kode transaksi tidak ditemukan'
            ], 400);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function decrementStock($product_id, $quantity, $tanggal)
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

                    $this->logStock('md_inter_product', $inter_product->id, 0, $stock_use, $tanggal);
                } else if ($ingredient->product_type == 1) {
                    // JIKA BAHAN BAKU
                    $material_id = $ingredient->material_id;
                    $material = Material::find($material_id);
                    $material->stock = $material->stock - $stock_use;
                    $material->save();

                    $this->logStock('md_material', $material->id, 0, $stock_use, $tanggal);
                }
            }

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logStock($table, $id, $stock_in, $stock_out, $tanggal)
    {
        try {
            $userId = Auth::user()->id ?? session('id');
            LogStock::create([
                'user_id' => $userId,
                'relation_id' => $id,
                'table_relation' => $table,
                'stock_in' => $stock_in,
                'stock_out' => $stock_out,
                'created_at' => $tanggal,
                'updated_at' => $tanggal
            ]);

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changeStatusPayment(Request $request)
    {
        $data = new ManajemenPesananController();
        $function = $data->bulk_payment_status($request);

        return $function;
    }

    public function createPiutang($data)
    {
        $customer = MdCustomer::find($data['customer_id']) ?? null;
        $customer_name = $customer ? $customer->name : $data['cust_name'];
        try {
            $input['date'] = date('Y-m-d', strtotime($data['custom_date'] ?? now()));
            $input['type'] = 'Piutang Jangka Pendek';
            $input['sub_type'] = 'Piutang Usaha (Accounts Receivable)';
            $input['name'] = "Penjualan $customer_name Piutang";
            $receivable_from = MlIncome::where('name', 'Penjualan Produk')->where('userid', userOwnerId())->first();
            $input['receivable_from'] = $receivable_from->id;

            $onlyOne = true;
            if ($data['payment_method'] == 'piutang-usaha') {
                $saveto = MlCurrentAsset::where('userid', userOwnerId())->where('code', 'piutang-usaha')->first()->id;
                // $onlyOne = false;
                $onlyOne = true;
            }
            if ($data['payment_method'] == 'piutang-marketplace') {
                $saveto = MlCurrentAsset::where('userid', userOwnerId())->where('code', 'piutang-marketplace')->first()->id;
                $onlyOne = true;
            }
            if ($data['payment_method'] == 'piutang-cod') {
                $saveto = MlCurrentAsset::where('userid', userOwnerId())->where('code', 'piutang-cod')->first()->id;
                $onlyOne = true;
            }

            $input['save_to'] = $saveto;
            $input['amount'] = $data['paid'];
            $input['note'] = "Penjualan $customer_name Piutang";
            $input['user_id'] = userOwnerId();
            $input['account_code_id'] = 7;
            $input['sync_status'] = 1;
            $input['penjualan_id'] = $data['id'];

            $checkPiutang = Receivable::where('receivable_from', $receivable_from->id)
                ->where('save_to', $saveto)
                ->where('user_id', userOwnerId())
                ->where('type', $input['type'])
                ->where('sub_type', $input['sub_type'])
                ->when($input['name'], function ($q) use ($input, $data) {
                    if ($data['payment_method'] == 'piutang-usaha') {
                        $q->where('name', $input['name']);
                    }
                })
                ->orderBy('id', 'desc')
                ->first();


            if ($data['payment_method'] == 'piutang-usaha') {
                if ($checkPiutang && $onlyOne && $checkPiutang->name == $input['name']) {
                    $update = Receivable::find($checkPiutang->id)->update([
                        'amount' => $checkPiutang->amount + $input['amount'],
                        'date' => $input['date'],
                    ]);
                } else {
                    $create = Receivable::create($input);
                }
            } else {
                if ($checkPiutang && $onlyOne) {
                    // if ($checkPiutang) {
                    $update = Receivable::find($checkPiutang->id)->update([
                        'amount' => $checkPiutang->amount + $input['amount'],
                        'date' => $input['date'],
                    ]);
                } else {
                    $create = Receivable::create($input);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFlagData(Request $request)
    {
        $input = $request->all();
        try {
            $query = PaymentMethodFlags::where('user_id', $this->get_owner_id($input['userid']));
            if ($input['code'] == 'piutang-usaha') {
                $query->where('group', 'Piutang');
            } else if ($input['code'] == 'piutang-cod') {
                $query->where('group', 'COD');
            } else if ($input['code'] == 'piutang-marketplace') {
                $query->where('group', 'Marketplace');
            } else {
                $query->where('payment_method', $input['code']);
            }

            $data = $query->get();
            return response()->json([
                "success" => true,
                "data" => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function rekapV2(Request $request)
    {

        $userid = $request->userid;
        $ownerId = $this->get_owner_id($userid);
        $branch_id = MlAccount::where('id', $ownerId)->first()->branch_id;
        $cek_user = MlAccount::find($userid);

        $userKey = $request->user_key ?? null;
        $columns = [
            'id',
            'user_id',
            'brach_id',
            'mt_kas_kecil_id',
            'initial_cash',
            'cash_sale',
            'transfer_sales',
            'payment_gateway_sales',
            'piutang_sales',
            'outlet_output',
            'total_cash',
            'total_sales',
            'created_at',
        ];

        $date = $request->date ?? date('Y-m-d');


        $query = MtRekapitulasiHarian::orderBy('id', 'desc')
            ->select($columns)
            ->whereDate('created_at', $date)
            ->with('kasKecil', 'user');
        if ($cek_user->role_code == 'staff') {
            $query->where('user_id', $userid);
        } else if ($cek_user->role_code == 'general_member') {
            $query->where('brach_id', $ownerId);
        }
        $data = $query->get();

        $info = \App\Models\MlAccountInfo::where('user_id',  $ownerId)->first();
        $method = $info->payment_method;

        $payment = json_decode($method);
        $banks = $payment[2]->banks;
        $piutang = [];
        array_push($piutang, $payment[3]);
        array_push($piutang, $payment[4]);
        array_push($piutang, $payment[5]);

        foreach ($data as $key => $value) {
            $jam_buka = date('H:i:s', strtotime($value->kasKecil->open_cashier_at));
            $jam_tutup = $value->kasKecil->close_cashier_at == null ? date('H:i:s') : date('H:i:s', strtotime($value->kasKecil->close_cashier_at));

            $awal = $date . ' ' . $jam_buka;
            $akhir = $date . ' ' . $jam_tutup;


            $branch_name = BusinessGroup::where('user_id', $ownerId)->first();
            $value['nama_toko'] = $branch_name->branch_name ?? $value->user()->first()->business_group->branch_name;

            $value['tunai'] = \App\Models\Penjualan::where('created_at', '>=', $awal)
                ->where('created_at', '<=', $akhir)
                ->where('custom_date', $date)
                ->where('payment_status', 1)
                ->where('staff_id', $value->user_id)
                ->where('payment_method', 'kas')
                ->sum('paid');

            $value['pg'] = \App\Models\Penjualan::where('created_at', '>=', $awal)
                ->where(
                    'created_at',
                    '<=',
                    $akhir
                )
                ->where('custom_date', $date)
                ->where('payment_status', 1)
                ->where('staff_id', $value->user_id)
                ->where('payment_method', 'randu-wallet')
                ->sum('paid');

            $value['transfer'] = \App\Models\Penjualan::where('created_at', '>=', $awal)
                ->where(
                    'created_at',
                    '<=',
                    $akhir
                )
                ->where('custom_date', $date)
                ->where('payment_status', 1)
                ->where('staff_id', $value->user_id)
                ->where('payment_method', 'LIKE', 'bank%')
                ->sum('paid');
            $bank_arr = [];
            foreach ($banks as $in => $bank) {

                if ($bank->selected == 'true') {
                    $row['id'] = $bank->id;
                    $row['bank'] = $bank->bank;
                    $row['remark'] = $bank->remark;
                    $row['bankOwner'] = $bank->bankOwner;
                    $row['bankAccountNumber'] = $bank->bankAccountNumber;
                    $row['selected'] = $bank->selected;

                    $penjualan = \App\Models\Penjualan::where('created_at', '>=', $awal)
                        ->where('created_at', '<=', $akhir)
                        ->where('custom_date', $date)
                        ->where('payment_method', $bank->remark)
                        ->where('payment_status', 1)
                        ->where('staff_id', $value->user_id);
                    $perbank = $penjualan->sum('paid');
                    $row['perbank'] = $perbank;
                    $flags = \App\Models\PaymentMethodFlags::where(
                        'payment_method',
                        $bank->remark,
                    )->where('user_id', $value->brach_id)->get();
                    $flags_arr = [];
                    foreach ($flags as $flag) {
                        $rw['id'] = $flag->id;
                        $rw['group'] = $flag->group;
                        $rw['flag'] = $flag->flag;

                        $perflag = \App\Models\Penjualan::where(
                            'created_at',
                            '>=',
                            $awal
                        )
                            ->where('created_at', '<=', $akhir)
                            ->where('custom_date', $date)
                            ->where('flag_id', $flag->id)
                            ->where('payment_status', 1)
                            ->where('staff_id', $value->user_id)
                            ->sum('paid');
                        $rw['perflag'] = $perflag;
                        array_push($flags_arr, $rw);
                    }
                    $row['flags'] = $flags_arr;
                    array_push($bank_arr, $row);
                }
            }
            $value['banks'] = $bank_arr;
            // $value['flag'] = $flags_arr;

            $piu = \App\Models\Penjualan::where('created_at', '>=', $awal)
                ->where('created_at', '<=', $akhir)
                ->where('custom_date', $date)
                ->where('payment_status', 1)
                ->where('staff_id', $value->user_id)
                ->where('payment_method', 'LIKE', 'piutang%')
                ->sum('paid');

            $value['piutang'] = $piu;

            $piutangs_arr = [];
            foreach ($piutang as $pi) {
                $rp['id'] = $pi->id;
                $rp['method'] = $pi->method;
                $rp['selected'] = $pi->selected;
                if ($pi->method == 'COD') {
                    $pma = 'piutang-cod';
                } elseif ($pi->method == 'Marketplace') {
                    $pma = 'piutang-marketplace';
                } elseif ($pi->method == 'Piutang') {
                    $pma = 'piutang-usaha';
                }

                $penjualan = \App\Models\Penjualan::where(
                    'created_at',
                    '>=',
                    $awal,
                )
                    ->where('created_at', '<=', $akhir)
                    ->where('custom_date', $date)
                    ->where('payment_method', $pma)
                    ->where('payment_status', 1)
                    ->where('staff_id', $value->user_id)
                    ->sum('paid');


                $rp['perkasbon'] = $penjualan;
                $flags = \App\Models\PaymentMethodFlags::where(
                    'group',
                    $pi->method,
                )
                    ->where('user_id', $value->brach_id)
                    ->get();


                $fk_array = [];
                foreach ($flags as $flag) {
                    $rf['id'] = $flag->id;
                    $rf['flag'] = $flag->flag;
                    $rf['group'] = $flag->group;
                    $perflag = \App\Models\Penjualan::where(
                        'created_at',
                        '>=',
                        $awal,
                    )
                        ->where('created_at', '<=', $akhir)
                        ->where('custom_date', $date)
                        ->where('flag_id', $flag->id)
                        ->where('payment_status', 1)
                        ->where('staff_id', $value->user_id)
                        ->sum('paid');
                    $rf['perflag'] = $perflag;
                    array_push($fk_array, $rf);
                }

                $rp['flags'] = $fk_array;
                array_push($piutangs_arr, $rp);
            }

            $value['kasbon'] = $piutangs_arr;
            $value['omset'] = \App\Models\Penjualan::where('created_at', '>=', $awal)
                ->where('custom_date', $date)
                ->where('created_at', '<=', $akhir)
                ->where('payment_status', 1)
                ->where('staff_id', $value->user_id)
                ->sum('paid');
        }

        return response()->json([
            "success" => true,
            "data" => $data,
            "userkey" => $userKey,
            "date" => $date,

        ]);
    }
}
