<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Jobs\CopyProductDataJob;
use App\Jobs\SendMailKeting;
use App\Jobs\SendVerificationEmail;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Str;

use App\Models\Category;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use App\Models\Branch;
use App\Models\FollowUp;
use App\Models\MlAccount;
use App\Models\MlAccountInfo;
use App\Models\owner_detail_users;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImages;
use App\Models\Storefront;
use App\Traits\WhatsappTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    use WhatsappTrait;

    public function logout(Request $request)
    {
        $request->session()->regenerate();
        $request->session()->invalidate();
        $request->session()->flush();
        return redirect('/frontend_login');
    }


    public function forgotPassword() {
        return view('main.lupa_password');
    }

    public function terimakasih()
    {
        return view('main.terimakasih_new');
    }

    public function account_activate(Request $request)
    {
        $input = $request->all();
        $cek = DB::table('mail_tokens')->where('token', $input['code'] ?? 0);
        if ($cek->count() == 1) {
            $data = $cek->first();
            $enc_email = SHA1($data->email);
            if ($enc_email == $input['id']) {
                $res = Account::where('email', $data->email)->first();

                $res->update([
                    'is_active' => 1,
                    'token' => Str::random(36),
                    'user_key' => $this->generateRandomString(8, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
                ]);

                DB::table('mail_tokens')->where('token', $input['code'])->delete();
                return Redirect::to('activation_success')->with('success', 'Congratulations your account has been activated now, Please Login!');
            } else {
                return Redirect::to('activation_success')->with('error', 'Failed, your email is invalid!');
            }
        } else {
            return Redirect::to('activation_success')->with('error', 'Failed, Token is invalid!');
        }
    }

    public function activation_success()
    {
        return view('main.activation_success');
    }

    public function login()
    {
        $view = 'login';
        $category = Category::all();
        // $district = DB::table('districts')->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')->join('regencies', 'regencies.id', '=', 'districts.regency_id')->join('provinces', 'provinces.id', '=', 'regencies.province_id')->get();

        // return view('main.login', compact('view'));
        return view('auth.login_new', compact('view', 'category'));
    }

    public function login_action(Request $request)
    {
        $input = $request->all();
        $rules = [
            'email' => 'string|required',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $account = Account::where(function ($query) use ($input) {
                $query->where('email', $input['email'])->orWhere('username', $input['email'])->orWhere('phone', $input['email']);
            })
                // ->whereIs_active(1)
                ->whereIs_soft_delete(0)
                ->first();

            if (!$account) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum terdaftar. Silakan daftar untuk membuat akun.',
                ]);
            }

            if ($account->is_active == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun anda belum aktif, Silahkan melakukan aktifasi terlebih dahulu.',
                ]);
            }

            if (Hash::check($input['password'], $account->password)) {
                if ($account->is_soft_delete == 1) {
                    // return Redirect::back()->with('error', "Account Not Found!");
                    return response()->json([
                        'status' => false,
                        'message' => 'Akun tidak ditemukan!',
                    ]);
                } else {
                    if ($account->is_active == 1) {
                        $generate_token = Str::random(36);
                        $position_id = $account->position_id;

                        $permissions = $this->getPermissions($position_id);

                        session(['id' => $account->id, 'username' => $account->username, 'name' => $account->fullname, 'email' => $account->email, 'token' => $generate_token, 'is_upgraded' => $account->is_upgraded, 'role' => $account->role_code, 'permissions' => $permissions, 'profile_picture' => $account->profile_picture]);

                        Account::where('id', $account->id)->update(['token' => $generate_token]);

                        Auth::login($account);

                        // CHECK FOLLOWUP
                        $this->cekFollowup();
                        if ($account->role_code != 'staff') {
                            $redirect = '/';
                            // return Redirect::to('/');
                        } else {
                            $redirect = url('/pos/index');
                        }

                        Session::put('user-id', $account->id);

                        return response()->json([
                            'status' => true,
                            'message' => 'Login Berhasil',
                            'redirect' => $redirect,
                        ]);
                    } else {
                        // return Redirect::back()->with('error', "Your account hasn't been activated yet!");
                        return response()->json([
                            'status' => false,
                            'message' => 'Akun Anda belum diaktifkan. Silakan periksa email/whatsapp Anda untuk tautan aktivasi atau kunjungi https://help.randu.co.id untuk bantuan.',
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Alamat email atau kata sandi yang Anda masukkan salah, silakan coba lagi!',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Anda belum terdaftar. Silakan daftar untuk membuat akun.',
                // 'message' => $e->getMessage()
            ]);
        }
    }

    public function login_action_with_admin(Request $request)
    {
        $input = $request->all();
        $rules = [
            'uuid' => 'string|required|exists:users,uuid',
            'token' => 'required|min:6',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try {
            $account = Account::where('uuid', $input['uuid'])->where('token', $input['token'])->first();

            if ($account != null) {
                if ($account->is_soft_delete == 1) {
                    // return Redirect::back()->with('error', "Account Not Found!");
                    return response()->json([
                        'status' => false,
                        'message' => 'Akun tidak ditemukan!',
                    ]);
                } else {
                    if ($account->is_active == 1) {
                        $generate_token = Str::random(36);
                        session(['id' => $account->id, 'username' => $account->username, 'name' => $account->fullname, 'email' => $account->email, 'token' => $generate_token, 'is_upgraded' => $account->is_upgraded, 'role' => $account->role_code]);

                        Account::where('id', $account->id)->update(['token' => $generate_token]);

                        // CHECK FOLLOWUP
                        $this->cekFollowup();
                        if ($account->role_code != 'staff') {
                            $redirect = '/';
                            // return Redirect::to('/');
                        } else {
                            $redirect = route('pos.index');
                        }

                        Session::put('user-id', $account->id);

                        return response()->json([
                            'status' => true,
                            'message' => 'Login Berhasil',
                            'redirect' => $redirect,
                        ]);
                    } else {
                        // return Redirect::back()->with('error', "Your account hasn't been activated yet!");
                        return response()->json([
                            'status' => false,
                            'message' => 'Akun Anda belum diaktifkan. Silakan periksa email/whatsapp Anda untuk tautan aktivasi atau kunjungi https://help.randu.co.id untuk bantuan.',
                        ]);
                    }
                }
            } else {
                // return Redirect::back()->with('error', "Alamat email atau kata sandi yang Anda masukkan salah, silakan coba lagi!");
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum terdaftar. Silakan daftar untuk membuat akun.',
                ]);
            }
        } catch (\Exception $e) {
            // return redirect()->back()->with('error', "Alamat email atau kata sandi yang Anda masukkan salah, silakan coba lagi!");
            return response()->json([
                'status' => false,
                'message' => 'Alamat email atau kata sandi yang Anda masukkan salah, silakan coba lagi!',
            ]);
        }
    }
    public function register(Request $request)
    {
        $input = $request->all();

        $referal = $request->ref == null ? '' : $request->ref;

        $view = 'register';
        $category = Category::all();
        $district = DB::table('districts')->select('districts.name AS distrik', 'regencies.name AS kabupaten', 'provinces.name AS provinsi')->join('regencies', 'regencies.id', '=', 'districts.regency_id')->join('provinces', 'provinces.id', '=', 'regencies.province_id')->get();
        return view('auth.register_new', compact('view', 'category', 'district', 'referal'));
    }

    public function getCustomerServiceId()
    {
        $csList = DB::table('md_customer_services')->where('is_active', 1)->where('is_admin', 1)->orderBy('id', 'asc')->pluck('id'); // Ambil daftar cs_id untuk user ini

        // Ambil id terakhir dari penjualan yang menggunakan customer service milik user ini
        $lastCSId = DB::table('ml_accounts')->whereIn('cs_id', $csList)->orderBy('id', 'desc')->value('cs_id');

        // Jika belum ada penjualan, kembalikan cs_id pertama dari daftar user ini
        if (!$lastCSId) {
            return $csList->first();
        }

        $nextCSId = $csList->first(function ($id) use ($lastCSId) {
            return $id > $lastCSId;
        });

        if (!$nextCSId) {
            // Jika tidak ada customer service berikutnya dalam daftar, kembali ke yang pertama
            $nextCSId = $csList->first();
        }

        return $nextCSId;
    }

    public function signup(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($request->all(), ['from_owner', 'owner_id', 'duplicate_product_from_user_id']);
            $from_owner = $request->from_owner ?? false;
            $owner_id = $request->owner_id ?? null;
            $duplicate_product_from_user_id = $request->duplicate_product_from_user_id ?? null;

            $uuid = (string) Str::uuid();
            $input['uuid'] = $uuid;
            $input['password'] = bcrypt($input['password']);
            $input['status'] = 0;
            $input['roles'] = 1;
            $input['role_code'] = 'general_member';
            $input['created'] = time();
            $rs = empty($input['referal_source']) ? 'RESKIN' : $input['referal_source'];

            $input['referal_source'] = $rs;
            $input['is_active'] = $from_owner ? 1 : 0;
            $input['referal_code'] = uniqid();
            $input['user_key'] = $this->generateRandomString(8, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            $input['phone'] = $input['whatsapp'];
            $input['pin'] = '123123';
            $input['cs_id'] = $this->getCustomerServiceId();
            if ($from_owner) {
                $own_user = DB::table('ml_accounts')->whereId($owner_id)->first();
                $input['is_upgraded'] = 1;
                $input['upgrade_expiry'] = $own_user->upgrade_expiry;
            }

            $query = Account::create($input);
            $id = $query->id;

            if ($from_owner) {
                owner_detail_users::create([
                    'owner_id' => $owner_id,
                    'user_id' => $id,
                ]);
            }

            if ($id == 1) {
                Account::where('id', $id)->update([
                    'roles' => 99,
                    'role_code' => 'administrator',
                ]);
            } else {
                if (DB::table('branches')->where('id', $id)->exists()) {
                    Account::where('id', $id)->update([
                        'branch_id' => $id,
                    ]);
                }
            }

            $bg = new \App\Models\BusinessGroup();
            $bg->user_id = $id;
            $bg->branch_name = empty($input['business_name']) ? $input['fullname'] : $input['business_name'];
            $bg->business_category = $input['category'];
            // $bg->business_district = $input['district'];
            $bg->business_address = $input['full_address'] ?? null;
            $bg->business_phone = empty($input['business_phone']) ? $input['whatsapp'] : $input['business_phone'];
            $bg->model = 'main';
            $bg->save();

            $this->insert_ml_current_assets($id);
            $this->insert_ml_fixed_assets($id);
            $this->insert_ml_accumulated_depreciation($id);
            $this->insert_ml_shortterm_debt($id);
            $this->insert_ml_longterm_debt($id);
            $this->insert_ml_capital($id);
            $this->insert_ml_income($id);
            $this->insert_ml_cost_good_sold($id);
            $this->insert_ml_selling_cost($id);
            $this->insert_ml_admin_general_fees($id);
            $this->insert_ml_non_business_income($id);
            $this->insert_ml_non_business_expenses($id);
            $this->insert_default_supplier($id);
            $this->createDummyUser($id, $from_owner, $request);

            $create_user_info = ['user_id' => $id, 'phone_number' => $input['whatsapp']];
            DB::table('ml_user_information')->insert($create_user_info);
            $tokenVerif = Str::random(64);

            $cek = DB::table('mail_tokens')->where('email', $input['email']);
            if ($cek->count() > 0) {
                DB::table('mail_tokens')
                    ->where('email', $input['email'])
                    ->update(['token' => $tokenVerif]);
            } else {
                DB::table('mail_tokens')->insert([
                    'email' => $input['email'],
                    'token' => $tokenVerif,
                ]);
            }

            if (!$from_owner) {
                if (env('MAIL_ACTIVE') == 'true') {
                    // Panggil job untuk mengirim email verifikasi
                    SendVerificationEmail::dispatch($input['email'], $input['fullname'], $tokenVerif)->onQueue('signup_verification');
                }

                // Panggil job untuk mengirim pesan WhatsApp
                SendWhatsAppMessage::dispatch($input['email'], $input['whatsapp'], $input['fullname'], $tokenVerif)->onQueue('signup_verification');

                if (env('MAIL_ACTIVE') == 'true') {
                    SendMailKeting::dispatch($input['email'], $input['whatsapp'], $input['fullname'])->onQueue('signup_verification');
                }
            }

            DB::commit();



            if (!$from_owner) {
                return response()->json([
                    'status' => true,
                    'redirect' => '/terimakasih',
                    'title' => 'Terima Kasih',
                    'html' => '<p>Kami telah mengirimkan pesan ke email <b>Anda</b> berisikan link untuk mengaktifkan akun Anda.</p>
              <h5>Mohon cek email Anda (folder inbox atau terkadang masuk ke folder sh5am),</span> dan klik link aktivasinya.</h5>',
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'Terima Kasih, data cabang baru berhasil dibuat',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi. Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateMaterials($user_id, $curren_user_id)
    {
        $material = DB::table('md_materials')->where('userid', $user_id)->first();

        if ($material) {
            $materialArray = json_decode(json_encode($material), true);
            unset($materialArray['id']);
            $materialArray['userid'] = $curren_user_id;

            $new_material_id = DB::table('md_product_category')->insertGetId($materialArray);
        }
    }

    public function createDummyUser($id, $from_owner = false, $request)
    {
        try {
            $res = Account::find($id);
            $duplicate_product_from_user_id = $request->duplicate_product_from_user_id ?? null;

            return $this->atomic(function () use ($res, $from_owner, $request, $duplicate_product_from_user_id) {
                DB::table('branches')->insert([
                    'id' => $res->id,
                    'name' => !$from_owner ? 'Cabang Pusat' : $request->fullname,
                    'address' => !$from_owner ? 'Jl. Raya ...' : $request->full_address ?? 'Jl. Raya ...',
                    'phone' => '08',
                    'account_id' => $res->id,
                    'created_at' => now(),
                ]);

                $dataAccountInfo = [
                    'id' => $res->id,
                    'user_id' => $res->id,
                    'store_address' => 'Jalan Raya ....',
                    'province_id' => '11',
                    'province_name' => null,
                    'city_id' => '444',
                    'city_name' => 'Surabaya',
                    'subdistrict_id' => '6161',
                    'subdistrict_name' => 'Wonokromo',
                    'payment_method' => json_encode([
                        [
                            'id' => '1',
                            'method' => 'Cash',
                            'selected' => 'true',
                        ],
                        [
                            'id' => '2',
                            'method' => 'Online-Payment',
                            'selected' => 'false',
                        ],
                        [
                            'id' => '3',
                            'method' => 'Transfer',
                            'selected' => 'true',
                            'banks' => [
                                [
                                    'id' => '1',
                                    'bank' => 'Bank BCA',
                                    'remark' => 'bank-bca',
                                    'bankOwner' => null,
                                    'bankAccountNumber' => null,
                                    'selected' => 'true',
                                ],
                                [
                                    'id' => '2',
                                    'bank' => 'Bank Mandiri',
                                    'remark' => 'bank-mandiri',
                                    'bankOwner' => null,
                                    'bankAccountNumber' => null,
                                    'selected' => 'true',
                                ],
                                [
                                    'id' => '3',
                                    'bank' => 'Bank BNI',
                                    'remark' => 'bank-bni',
                                    'bankOwner' => null,
                                    'bankAccountNumber' => null,
                                    'selected' => 'true',
                                ],
                                [
                                    'id' => '4',
                                    'bank' => 'Bank BRI',
                                    'remark' => 'bank-bri',
                                    'bankOwner' => null,
                                    'bankAccountNumber' => null,
                                    'selected' => 'true',
                                ],
                                [
                                    'id' => '5',
                                    'bank' => 'OVO',
                                    'remark' => 'bank-lain',
                                    'bankOwner' => null,
                                    'bankAccountNumber' => null,
                                    'selected' => 'true',
                                ],
                            ],
                        ],
                        [
                            'id' => '4',
                            'method' => 'COD',
                            'selected' => 'false',
                        ],
                        [
                            'id' => '5',
                            'method' => 'Marketplace',
                            'selected' => 'false',
                        ],
                        [
                            'id' => '6',
                            'method' => 'Piutang',
                            'selected' => 'false',
                        ],
                        [
                            'id' => '7',
                            'method' => 'QRIS',
                            'selected' => 'true',
                        ],
                    ]),
                    'shipping' => json_encode([
                        ['id' => '1', 'method' => 'POS', 'selected' => false],
                        ['id' => '2', 'method' => 'LION', 'selected' => false],
                        ['id' => '3', 'method' => 'NINJA', 'selected' => false],
                        ['id' => '4', 'method' => 'IDE', 'selected' => true],
                        ['id' => '5', 'method' => 'SICEPAT', 'selected' => false],
                        ['id' => '6', 'method' => 'SAP', 'selected' => false],
                        ['id' => '7', 'method' => 'NCS', 'selected' => false],
                        ['id' => '8', 'method' => 'ANTERAJA', 'selected' => false],
                        ['id' => '9', 'method' => 'REX', 'selected' => false],
                        ['id' => '10', 'method' => 'JTL', 'selected' => false],
                        ['id' => '11', 'method' => 'SENTRAL', 'selected' => false],
                        ['id' => '12', 'method' => 'JNE', 'selected' => true],
                        ['id' => '13', 'method' => 'TIKI', 'selected' => false],
                        ['id' => '14', 'method' => 'RPX', 'selected' => false],
                        ['id' => '15', 'method' => 'PANDU', 'selected' => false],
                        ['id' => '16', 'method' => 'WAHANA', 'selected' => false],
                        ['id' => '17', 'method' => 'JNT', 'selected' => false],
                        ['id' => '18', 'method' => 'PAHALA', 'selected' => false],
                        ['id' => '19', 'method' => 'SLIS', 'selected' => false],
                        ['id' => '20', 'method' => 'EXPEDITO', 'selected' => false],
                        ['id' => '21', 'method' => 'RAY', 'selected' => false],
                        ['id' => '22', 'method' => 'DSE', 'selected' => false],
                        ['id' => '23', 'method' => 'FIRST', 'selected' => false],
                        ['id' => '24', 'method' => 'STAR', 'selected' => false],
                        ['id' => '25', 'method' => 'IDL', 'selected' => false],
                    ]),
                ];

                DB::table('ml_account_info')->insert($dataAccountInfo);

                $dataProductCategorys = [
                    [
                        'code' => 'MKNN',
                        'name' => 'Makanan',
                        'created' => now(),
                        'user_id' => $res->id,
                        'image' => 'dummy_products.jpg',
                        'description' => 'Ini adalah contoh kategori, silakan di edit sesuai kebutuhan',
                    ],
                    [
                        'code' => 'SMBK',
                        'name' => 'Sembako',
                        'created' => now(),
                        'user_id' => $res->id,
                        'image' => 'dummy_product_sembako.jpg',
                        'description' => 'Ini adalah contoh kategori, silakan di edit sesuai kebutuhan',
                    ],
                    [
                        'code' => 'FSHN',
                        'name' => 'Fashion',
                        'created' => now(),
                        'user_id' => $res->id,
                        'image' => 'dummy_product_fashion.jpg',
                        'description' => 'Ini adalah contoh kategori, silakan di edit sesuai kebutuhan',
                    ],
                ];

                $dataProducts = [
                    [
                        'code' => null,
                        'sku' => 'NGC',
                        'barcode' => null,
                        'name' => 'Nasi Goreng (Contoh)',
                        'price' => 15000.0,
                        'cost' => 7000.0,
                        'default_cost' => 7000.0,
                        'unit' => 'Portion (Porsi)',
                        'quantity' => 0,
                        'stock_alert' => 0,
                        'sell' => 0,
                        'created' => now(),
                        'user_id' => $res->id,
                        'is_variant' => 1,
                        'is_manufactured' => 1,
                        'buffered_stock' => 0,
                        'weight' => 100,
                        'description' => '<p>Nasi goreng, sajian ikonik Nusantara, kini hadir dalam versi spesial yang lebih istimewa! Terbuat dari nasi pilihan yang dimasak hingga sempurna, dicampur dengan bumbu rahasia khas kami, membuat setiap suapan memberikan sensasi rasa yang luar biasa. Ditambah lagi dengan potongan ayam suwir, udang segar, dan irisan sayuran renyah, Nasi Goreng Spesial ini disajikan hangat untuk memanjakan selera Anda. Cocok dinikmati kapan saja, Nasi Goreng Spesial kami siap memenuhi kebutuhan selera Anda akan masakan Indonesia yang autentik dan lezat.</p>',
                        'created_by' => 0,
                    ],
                    [
                        'code' => null,
                        'sku' => 'BRS5',
                        'barcode' => null,
                        'name' => 'Beras 5 Kg (Contoh)',
                        'price' => 72000.0,
                        'cost' => 71000.0,
                        'default_cost' => 71000.0,
                        'unit' => 'Pack (Paket)',
                        'quantity' => 0,
                        'stock_alert' => 0,
                        'sell' => 0,
                        'created' => now(),
                        'user_id' => $res->id,
                        'is_variant' => 1,
                        'is_manufactured' => 1,
                        'buffered_stock' => 0,
                        'weight' => 5000,
                        'description' => '<p>Beras 5 kg adalah bahan pokok berupa butiran padi yang telah melalui proses penggilingan dan penyosohan untuk menjadi beras siap masak. Beras ini cocok untuk kebutuhan sehari-hari rumah tangga, dengan bobot kemasan 5 kilogram yang ideal untuk penggunaan dalam jangka waktu tertentu. Varietas beras dapat bervariasi, seperti beras putih, beras merah, atau beras organik, dengan tekstur dan aroma yang disesuaikan dengan selera konsumsi.</p>',
                        'created_by' => 0,
                    ],
                    [
                        'code' => null,
                        'sku' => 'JKT',
                        'barcode' => null,
                        'name' => 'Jaket (Contoh)',
                        'price' => 99000.0,
                        'cost' => 80000.0,
                        'default_cost' => 80000.0,
                        'unit' => 'Pieces (pcs)',
                        'quantity' => 0,
                        'stock_alert' => 0,
                        'sell' => 0,
                        'created' => now(),
                        'user_id' => $res->id,
                        'is_variant' => 1,
                        'is_manufactured' => 1,
                        'buffered_stock' => 0,
                        'weight' => 1000,
                        'description' => '<p>Jaket adalah pakaian luar yang dirancang untuk memberikan kehangatan dan perlindungan dari cuaca dingin atau angin. Biasanya terbuat dari bahan yang tebal seperti wol, katun, atau sintetis, jaket memiliki berbagai gaya, mulai dari kasual hingga formal. Jaket sering dilengkapi dengan ritsleting atau kancing di bagian depan, saku, dan kadang-kadang tudung (hoodie) untuk melindungi kepala. Cocok digunakan dalam berbagai kesempatan, jaket juga menjadi bagian penting dalam gaya berpakaian sehari-hari.</p>',
                        'created_by' => 0,
                    ],
                ];

                $imageProducts = [
                    [
                        'url' => 'dummy_products.jpg',
                        'main' => '1',
                    ],
                    [
                        'url' => 'dummy_product_sembako.jpg',
                        'main' => '1',
                    ],
                    [
                        'url' => 'dummy_product_fashion.jpg',
                        'main' => '1',
                    ],
                ];

                if (!$duplicate_product_from_user_id) {
                    foreach ($dataProductCategorys as $key => $category) {
                        $md_product_category = ProductCategory::create($category);

                        $createDataProduct = $dataProducts[$key];
                        $createDataProduct['category_id'] = $md_product_category->id;
                        $md_product = Product::create($createDataProduct);

                        $createDataImageProduct = $imageProducts[$key];
                        $createDataImageProduct['product_id'] = $md_product->id;
                        ProductImages::create($createDataImageProduct);
                    }
                } else {
                    if ($from_owner && $duplicate_product_from_user_id) {
                        CopyProductDataJob::dispatch($res->id, $duplicate_product_from_user_id)->onQueue('clone_branch');
                    }
                }

                $dataStoreFront = [
                    'user_id' => $res->id,
                    'banner' => null,
                    'template' => 'FNB',
                    'banner_image1' => 'storefront-banner1-default.jpg',
                    'banner_link1' => 'https://randu.co.id',
                    'banner_image2' => 'storefront-banner2-default.jpg',
                    'banner_link2' => 'https://randu.co.id',
                    'banner_image3' => 'storefront-banner3-default.jpg',
                    'banner_link3' => 'https://randu.co.id',
                    'delivery' => 0,
                ];

                Storefront::create($dataStoreFront);

                return true;
            });
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return false;
        }
    }

    public function insert_default_supplier($id)
    {
        $datas = ['Gudang Persediaan Bahan Baku', 'Gudang Persediaan Barang Setengah jadi'];

        foreach ($datas as $data) {
            $supplier = new \App\Models\Supplier();
            $supplier->userid = $id;
            $supplier->name = $data;
            $supplier->contact_name = $data;
            $supplier->phone = '-';
            $supplier->email = uniqid() . '@mail.com';
            $supplier->fax = '';
            $supplier->website = '';
            $supplier->jalan1 = $data;
            $supplier->jalan2 = '';
            $supplier->postal_code = '';
            $supplier->province = '0';
            $supplier->country = '';
            $supplier->can_be_deleted = 0;
            $supplier->created_at = date('Y-m-d H:i:s');
            $supplier->updated_at = date('Y-m-d H:i:s');
            $supplier->save();
        }
    }

    public function insert_ml_current_assets($userid)
    {
        $data = ['Kas', 'Bank BCA', 'Bank Mandiri', 'Bank BRI', 'Bank BNI', 'Piutang COD', 'Piutang Marketplace', 'Perlengkapan', 'Persediaan Bahan Baku', 'Persedian Barang Setengah Jadi', 'Persediaan Barang Dagang', 'Piutang Usaha', 'Sewa Bayar Dimuka', 'Iklan Bayar Dimuka', 'Randu Wallet'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_current_assets')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 1, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_fixed_assets($userid)
    {
        $data = ['Tanah', 'Bangunan', 'Kendaraan', 'Peralatan'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_fixed_assets')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 2, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_accumulated_depreciation($userid)
    {
        $data = ['Akumulasi Penyusutan Kendaraan', 'Akumulasi Penyusutan Peralatan', 'Akumulasi Penyusutan Bangunan'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_accumulated_depreciation')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 3, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_shortterm_debt($userid)
    {
        $data = ['Utang Usaha'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_shortterm_debt')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 4, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_longterm_debt($userid)
    {
        $data = ['Utang Bank'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_longterm_debt')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 5, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_capital($userid)
    {
        $data = ['Modal Pemilik', 'Prive'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_capital')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 6, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_income($userid)
    {
        $data = ['Pendapatan', 'Penjualan Produk', 'Ikhtisar Laba/Rugi', 'Potongan Penjualan', 'Retur Penjualan'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_income')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 7, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_cost_good_sold($userid)
    {
        $data = ['Harga Pokok Penjualan', 'Potongan Pembelian', 'Retur Pembelian'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_cost_good_sold')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 8, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_selling_cost($userid)
    {
        $data = ['Biaya Bonus Penjualan', 'Biaya Pengiriman', 'Biaya Penjualan Lain-Lain', 'Biaya Pajak Penjualan', 'Biaya Iklan', 'Biaya Retur Penjualan', 'Biaya Kerusakan Barang'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_selling_cost')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 9, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_admin_general_fees($userid)
    {
        $data = ['Biaya Air', 'Biaya Depresiasi Peralatan', 'Biaya Gaji Karyawan', 'Biaya Listrik', 'Biaya Makan dan Minum', 'Biaya Perlengkapan', 'Biaya Sewa Tempat Usaha', 'Biaya Telepon', 'Biaya Internet', 'Biaya Umum Lain-Lain', 'Biaya Penyusutan Bangunan', 'Biaya Penyusutan Kendaraan', 'Biaya Penyusutan Peralatan', 'Beban Piutang Tak Tertagih', 'Biaya Diluar Usaha'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_admin_general_fees')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 10, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_non_business_income($userid)
    {
        $data = ['Pendapatan Bunga Bank', 'Pendapatan Hasil Panen'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_non_business_income')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 11, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function insert_ml_non_business_expenses($userid)
    {
        $data = ['Biaya Administrasi Bank', 'Biaya Lain Lain'];

        foreach ($data as $key => $value) {
            $get_code = str_replace(' ', '-', strtolower($value));
            $get_code = str_replace('&', '-', $get_code);
            $get_code = str_replace('/', '-', $get_code);

            DB::table('ml_non_business_expenses')->insert(['userid' => $userid, 'name' => $value, 'code' => $get_code, 'account_code_id' => 12, 'can_be_deleted' => 1, 'created' => time()]);
        }
    }

    public function sendMail($email, $name, $token)
    {
        $details = [
            'nama' => $name,
            'email' => $email,
            'link' => $token,
            'id' => SHA1($email),
        ];

        Mail::to($email)->send(new RegisterMail($details));
    }

    public function sendWa($email, $phone, $name, $token)
    {
        $id = SHA1($email);
        $link = $token;
        $app_url = str_replace('https://', '', env('APP_URL'));

        $message = "*Yth. Bapak/Ibu $name*\n";
        $message .= "Terima kasih telah bergabung bersama platform akunting terbaik abad ini $app_url \n\n";
        $message .= "Untuk mengaktifkan akun anda silahkan klik link dibawah ini (atau copy paste ke browser anda)\n";
        $message .= url('account_activate') . "?id=$id&code=$link \n\n";
        $message .= "setelah itu gunakan email dan password anda untuk masuk di link bawah ini \n" . url('/frontend_login') . "\n\n";
        $message .= "Salam, \n*Admin Randu.co.id*";

        // Using trait method instead of direct cURL
        return $this->sendWhatsappMessage($phone, $message);
    }

    public function cekFollowup()
    {
        $name = [
            // FOLLOWUP
            [
                'name' => 'Text Welcome',
                'type' => 'followup',
                'text' => 'Hai kak [name] makasih udah tertarik dengan produk [productname]. Nama saya [customerservice]. Jika berkenan simpan nomor [customerservice] ya 😊

[productname] [variable] [bumb]
Harga: [total_payment]

Akan dikirim ke:
Nama: [name]
No HP: [phone_number]
Alamat: [address]
Desa:
Kecamatan:
Kota/Kabupaten: [city]

Bantu [customerservice] untuk melengkapi data diatas untuk pengiriman pesanan kakak [name] agar [customerservice]  bisa menghitungkan total harga + ongkos kirimnya.

Biasanya kakak [name] transaksi COD atau via Transfer?',
            ],
            [
                'name' => 'Whatsapp Follow Up 1',
                'type' => 'followup',
                'text' => 'Kak [name] pesanan kemaren belum ada konfirmasi, Apakah ada yang ingin di tanyakan lagi tentang pesanan [productname] kak [name]?
Senang sekali jika [customerservice] bisa menjawab. Mungkin tentang bahannya atau tentang hal lain?',
            ],
            [
                'name' => 'Whatsapp Follow Up 2',
                'type' => 'followup',
                'text' => 'Haii...
Bagaimana kabarnya kak [name] hari ini? Semoga sehat selalu ya...

[customerservice] cek status pembayarannya masih pending ya?
Apakah ada yang masih belum jelas tentang [productname]?',
            ],
            [
                'name' => 'Whatsapp Follow Up 3',
                'type' => 'followup',
                'text' => 'Mohon maaf sekali [customerservice] belum menerima konfirmasi dari kak [name] sampai hari ini.
Jika sedang tidak ada waktu buat transfer, [productname] bisa bayar ditempat atau COD loh kak. Biasanya kakak bayar lewat transfer atau COD?',
            ],
            [
                'name' => 'Whatsapp Follow Up 4',
                'type' => 'followup',
                'text' => 'Halo kak [name] udah hari ke 4 nih [customerservice] ngechat, tapi mohon maaf [customerservice] belum juga menerima konfirmasi pesanan produk [productname]

Hari ini [customerservice] kasih diskon ongkir 20% deh... Gimana kak?',
            ],
            [
                'name' => 'Whatsapp Follow Up 5',
                'type' => 'followup',
                'text' => 'Kak [name], hari ini [customerservice] lagi kejar target nih, barangkali produk [productname] yang kapan hari kakak berminat mau dikonfirmasi pembeliannya? atau masih ada yang ingin ditanyakan lagi?

Atau jika mau batalpun gpp kog kak, tinggal bilang ke [customerservice], nanti  [customerservice] batalin pesanannya.',
            ],
            [
                'name' => 'Whatsapp Follow Up 6',
                'type' => 'followup',
                'text' => 'Halo kak [name], barangkali produk yang kemaren batal kakak mungkin tertarik dengan produk kita yang lain?',
            ],
            [
                'name' => 'Whatsapp Follow Up 7',
                'type' => 'followup',
                'text' => 'Kak [name]... Kalau masih ragu dengan kualitas [productname] ada garansinya loh, bahkan 7 hari setelah pembelian masih bisa kakak tukarkan jika tidak puas dengan kualitasnya. Gimana kak? Apakah mau diproses pembeliannya?',
            ],
            [
                'name' => 'Whatsapp Follow Up 8',
                'type' => 'followup',
                'text' => 'Kak [name], hari ini [customerservice] lagi kejar target nih, barangkali produk [productname] yang kapan hari kakak berminat mau dikonfirmasi pembeliannya? atau masih ada yang ingin ditanyakan lagi?

Atau jika mau batalpun gpp kog kak, tinggal bilang ke [customerservice], nanti  [customerservice] batalin pesanannya.',
            ],
            [
                'name' => 'Whatsapp Follow Up 9',
                'type' => 'followup',
                'text' => 'Produk [productname] yang kakak beli kapah hari apakah masih minat untuk diproses dan dikirimkan ke alamat kakak?
Buat closingan [customerservice] nih kak... [customerservice] doakan kalau kakak  [name] jadi beli, kakak rejekinya berkali-kali lipat. [customerservice] jamin gak bakalan kecewa dengan produknya. Mau kak?',
            ],
            [
                'name' => 'Whatsapp Follow Up 10',
                'type' => 'followup',
                'text' => 'Hallo kak [name]... Ijin follow up, untuk pembelian [productname] apakah jadi? Karena ini sudah hari ke 10 dan [customerservice] belum mendapat jawaban dari kaka.

Jika ada kendala, bisa sampaikan ke [customerservice] ya',
            ],

            // UPSELLING
            [
                'name' => 'Text Success',
                'type' => 'upselling',
                'text' => 'Terima kasih kak [name]. Pesanan kakak [name]: [productname] akan segera dipacking dan dikirim. Mohon ditunggu kedatangan kurir dalam 2 atau 3 hari (tidak termasuk hari libur) dan resi pengiriman akan [customerservice] segera kirimkan dalam 1x24 jam (Weekday)

Sehat selalu dan rejekinya berlimpah buat kakak [name]',
            ],
            [
                'name' => 'Text COD',
                'type' => 'upselling',
                'text' => 'Terima kasih kak [name]. Pesanan kakak [name] akan segera dipacking dan dikirim dengan sistem COD

*Hak Kakak [name]* Sebagai konsumen kak [name] berhak menerima produk yang sesuai dengan yang di iklankan. Jika nantinya ada spesifikasi yang tidak sesuai (warna, ukuran, bahan DLL) kakak berhak mengejukan pengembalian (tukar barang / pengembalian uang.

*Kewajiban Kakak [name]* Sebagaimana diatur dalam *Pasal 1266, 1267, dan 1517 KUH Perdata* tentang transaksi COD, Kak [name] *tidak boleh* membatalkan COD dengan alasan apapun. Jika terpaksa meninggalkan rumah atau berhalangan kakak bisa chat [customerservice] untuk mengatur pengiriman ulang. Hal-hal seperti komplain warna, ukuran, bahan DLL di diskusikan dengan [customerservice] *setelah* paket COD dibayar.

Sehat selalu dan rejekinya berlimpah buat kakak [name]. Aminnn.',
            ],
            [
                'name' => 'Whatsapp Upselling 1',
                'type' => 'upselling',
                'text' => 'Halo kak [name], gimana produk pesanan [productname]  kak [name] kemaren? Suka ga? Boleh minta testimoninya?

Sekaligus [customerservice] mau mengucapkan terima kasih banyak buat kak {name} karena dengan [productname], saya juga mendapatkan komisi dan gaji untuk keluarga saya.

Doa saya yang banyak dan tulus semoga produknya bermanfaat dan rejekinya kak {name}  lancar terus 😊 Amin...',
            ],
            [
                'name' => 'Whatsapp Upselling 2',
                'type' => 'upselling',
                'text' => 'Hi [Name]! Lagi butuh bahan masakan berkualitas? Kami punya paket fresh food dengan harga spesial. Tertarik? 🍅🥦',
            ],
            [
                'name' => 'Whatsapp Upselling 3',
                'type' => 'upselling',
                'text' => 'Halo [Name], koleksi fashion terbaru kami baru saja tiba! Dapatkan diskon 15% kalau kamu pesan hari ini. Cek yuk! 👗👖',
            ],
            [
                'name' => 'Whatsapp Upselling 4',
                'type' => 'upselling',
                'text' => 'Hi [Name], stok produk rumah tangga kami sedang diskon besar-besaran. Yuk, isi ulang kebutuhan rumah kamu dengan harga hemat! 🏠🧼',
            ],
            [
                'name' => 'Whatsapp Upselling 5',
                'type' => 'upselling',
                'text' => 'Halo [Name], sudah coba camilan sehat terbaru kami? Lagi ada promo beli 2 gratis 1 lho. Jangan sampai ketinggalan! 🥗🍪',
            ],
            [
                'name' => 'Whatsapp Upselling 6',
                'type' => 'upselling',
                'text' => 'Hai [Name], koleksi sepatu baru kami siap menemani aktivitas kamu. Pesan sekarang dan dapatkan diskon 20%! 👟👠',
            ],
            [
                'name' => 'Whatsapp Upselling 7',
                'type' => 'upselling',
                'text' => 'Hey [Name], peralatan dapur berkualitas lagi ada promo spesial nih. Upgrade peralatan dapur kamu sekarang juga! 🍳🥄',
            ],
            [
                'name' => 'Whatsapp Upselling 8',
                'type' => 'upselling',
                'text' => 'Hi [Name], butuh minuman segar? Kami punya jus sehat yang lagi diskon. Pesan sekarang dan rasakan kesegarannya! 🥤🍹',
            ],
            [
                'name' => 'Whatsapp Upselling 9',
                'type' => 'upselling',
                'text' => 'Halo [Name], koleksi baju anak terbaru sudah hadir. Pesan sekarang dan dapatkan diskon khusus untuk pelanggan setia seperti kamu! 👶🍼',
            ],
            [
                'name' => 'Whatsapp Upselling 10',
                'type' => 'upselling',
                'text' => 'Hai [Name], mau tampilan baru di rumah? Dekorasi rumah kami lagi diskon hingga 30%! Yuk, hias rumah kamu sekarang juga. 🏡🖼️',
            ],
        ];

        foreach ($name as $key => $value) {
            $check = FollowUp::where('account_id', session('id'))->where('name', $value['name'])->where('type', $value['type'])->first();

            if (!$check) {
                FollowUp::create([
                    'name' => $value['name'],
                    'text' => $value['text'],
                    'account_id' => session('id'),
                    'type' => $value['type'],
                ]);
            }
        }
    }

    private function getPermissions(?int $position_id): ?array
    {
        if (!is_null($position_id)) {
            $permissions = DB::table('staff_position_permissions')->leftJoin('permissions', 'permissions.id', '=', 'staff_position_permissions.permission_id')->where('staff_position_id', $position_id)->select('permissions.name')->orderBy('permissions.name', 'asc')->get()->pluck('name')->toArray();
            return $permissions;
        } else {
            return [];
        }
    }

    public function loginAsUser(Request $request)
    {
        $request->session()->regenerate();
        $request->session()->invalidate();
        $request->session()->flush();

        $token = $request->encryption;
        $account = Account::where('token', $token)->first();

        if (!$account) {
            return redirect()
                ->back()
                ->withErrors(['User not found.']);
        }

        do {
            $generate_token = Str::random(60);
        } while (Account::where('token', $generate_token)->exists());

        $position_id = $account->position_id;
        $permissions = $this->getPermissions($position_id);
        session(['id' => $account->id, 'username' => $account->username, 'name' => $account->fullname, 'email' => $account->email, 'token' => $generate_token, 'is_upgraded' => $account->is_upgraded, 'role' => $account->role_code, 'permissions' => $permissions]);
        $account->token = $generate_token;
        $account->save();

        // Redirect to the user's home page or dashboard
        return redirect('/')->with('success', 'Logged in as user successfully.');
    }

    public function mobileAccountRemove()
    {
        return view('account_delete');
    }

    public function mobileAccountPost(Request $request)
    {
        $input = $request->all();

        $cek = MlAccount::where('email', $input['email']);
        if ($cek->count() > 0) {
            return redirect()
                ->back()
                ->with(['success' => 'Terima kasih, Pengajuan penghapusan akun anda telah kami terima, selanjutkan kami akan memberikan pertanyaan berkenaan dengan akun Anda melalui email yang terdaftar.']);
        } else {
            return redirect()->back()->with('error', 'maaf email tidak terdaftar!!');
        }
    }
}
