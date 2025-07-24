<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Main\AccountController;
use App\Http\Requests\RegisterRequest;
use App\Jobs\CopyProductDataJob;
use App\Jobs\ResetAccountService;
use App\Jobs\SendResetAccountEmail;
use App\Jobs\SendWhatsAppMessageGlobal;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Broadcast;
use App\Models\BusinessGroup;
use App\Models\MlAccount;
use App\Models\MlSettingUser;
use App\Models\MobileVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
                // 'token_fcm' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('password');
            $user = Account::where(function ($query) use ($request) {
                $query->where('email', $request['email'])
                    ->orWhere('username', $request['email'])
                    ->orWhere('phone', $request['email']);
            })
                ->when($request->has('role_code'), function ($query) use ($request) {
                    if ($request['role_code'] == 'owner') {
                        $query->where('role_code', $request['role_code']);
                    } else {
                        $query->where('role_code', '!=', 'owner');
                    }
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Anda belum terdaftar. Silakan daftar untuk membuat akun.',
                ], 400);
            }

            if ($user->is_active == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun anda belum aktif, Silahkan melakukan aktifasi terlebih dahulu.',
                ], 400);
            }

            if ($user->is_soft_delete == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akun tidak ditemukan!',
                ], 400);
            }

            if (isset($request['token_fcm'])) {
                $broadcast = Broadcast::where('user_id', $user->id)->where('token_fcm', $request['token_fcm'])->first();
                if (!$broadcast) {
                    Broadcast::create([
                        'user_id' => $user->id,
                        'token_fcm' => $request['token_fcm']
                    ]);
                }
            }

            if ($user && Hash::check($credentials['password'], $user->password)) {
                Auth::login($user);
                $token = $user->createToken('API Token')->accessToken;

                if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {
                    return response([
                        'message' => 'Email must be verified.'
                    ], 401);
                }

                return response([
                    'status' => true,
                    'message' => 'Login success.',
                    'token' => $token,
                    'user' => $user
                ]);
            }
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        return response([
            'message' => 'Periksa kembali email dan kata sandi.'
        ], 401);
    }

    public function user()
    {
        $user = Auth::user()->id ?? session('id');
        $userId = $this->get_owner_id($user);

        $account = MlAccount::where('id', $user)->first();
        // if (!isset($account->mlSettingUser)) {
        //     $createSettingUser = MlSettingUser::create([
        //         'user_id' => $account->id,
        //     ]);
        // }
        $userOwner = MlAccount::where('id', $user)->first();
        $accountOwner = MlAccount::where('id', $userId)->first();
        $bussiness_group = BusinessGroup::where('user_id', $userId)->first();
        $account['logo'] = ($accountOwner->is_upgraded == 1) ? $bussiness_group->logo : '';
        $account['bussines_name'] = $bussiness_group->branch_name ?? '-';
        $account['business_address'] = $bussiness_group->business_address ?? '-';
        $branch = Branch::where('id', $account->branch_id)->first();
        $account['branch_name'] = $branch->name ?? '-';
        $mlSettingUserObject = MlSettingUser::where('user_id', $userId)->first();
        $account['printer_connection'] = $mlSettingUserObject->printer_connection ?? '-';
        $account['printer_paper_size'] = $mlSettingUserObject->printer_paper_size ?? '-';
        $account['printer_custom_footer'] = $mlSettingUserObject->printer_custom_footer ?? '-';
        $mlSettingUser = $mlSettingUserObject; // Modifikasi nilai
        $account['ml_setting_user'] = $mlSettingUser; // Set ulang properti
        $account['tax'] = $accountOwner->tax ?? '-';

        return response()->json([
            'status' => true,
            'message' => 'Success.',
            'data' => $account,
        ]);
    }

    public function getUsers(Request $request)
    {
        $search = $request->input('q');
        $users = DB::table('owner_detail_users as main')
            ->selectRaw('users.*')
            ->join('ml_accounts as users', 'users.id', '=', 'main.user_id')
            ->whereOwner_id(Auth::user()->id);

        if ($search) {
            $users = $users->where("users.{$request->search_for}", 'LIKE', "%{$search}%");
        }

        $users = $users->get();

        return response()->json($users);
    }

    public function ownRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string',
                'username' => 'required|string',
                'email' => 'required|string',
                'business_address' => 'nullable|string',
                'business_phone' => 'nullable|min:8',
                'duplicate_product_from_user_id' => 'nullable|integer',
                'owner_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                "fullname" => $request->fullname,
                "username" => $request->username,
                "email" => $request->email,
                "whatsapp" => '',
                "password" => 123123,
                "password_confirmation" => 123123,
                "category" => '',
                "business_name" => $request->business_name,
                "full_address" => $request->business_address,
                "business_phone" => $request->business_phone,
                "referal_source" => null,
                "tos" => 'on',
                "from_owner" => true,
                "owner_id" => $request->owner_id,
                "duplicate_product_from_user_id" => $request->duplicate_product_from_user_id,
            ];

            $controller = new AccountController();
            $response = $controller->signup(new RegisterRequest($data));

            // Mengecek apakah respons sukses
            if ($response->getData()->status) {
                // Lakukan sesuatu jika sukses, misalnya mengembalikan respons
                return response([
                    'status' => true,
                    'message' => 'Registration success.',
                    'data' => $response->getData()->message
                ]);
            } else {
                // Lakukan sesuatu jika gagal
                trigger_error('Registrasi gagal.');
            }
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cloneBranch(Request $request)
    {
        $from_branch_user_id = $request->from_branch_user_id;
        $to_branch_user_id = $request->to_branch_user_id;

        CopyProductDataJob::dispatch($to_branch_user_id, $from_branch_user_id)->onQueue('clone_branch');

        return response()->json([
            'status' => true,
            'message' => 'Branch cloning has been processed.'
        ]);
    }

    public function accountReset()
    {
        $userid = session('id');
        $user = DB::table('ml_accounts')->whereId($userid)->first();
        $phone = $user->phone;
        $name = $user->fullname;

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        Session::put('user-id-' . $userid, $otp);

        $message = "*Yth. Bapak/Ibu $name*,\n";
        $message .= "Kami mengerti bahwa Anda mungkin perlu mengatur ulang akun Anda.\n";
        $message .= "Untuk melakukannya, silakan gunakan kode OTP berikut:\n";
        $message .= "Kode OTP Anda adalah: *$otp*\n\n";
        $message .= "Setelah anda memasukan kode otp, sistem akan mengatur ulang akun anda dan silahkan tunggu beberapa menit. :\n";
        $message .= "Jika Anda mengalami kesulitan, hubungi trainer melalui Livechat di https://help.randu.co.id\n";
        $message .= "Salam hangat,\n*Istabel dari Randu*";

        SendWhatsAppMessageGlobal::dispatch($phone, $message)->onQueue('account_reset');

        if (env('MAIL_ACTIVE') == 'true') {
            // Panggil job untuk mengirim email verifikasi
            SendResetAccountEmail::dispatch($user->email, $user->fullname, $otp)->onQueue('account_reset');
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function accountResetStartAction(Request $request)
    {
        $userid = session('id');
        $otpCur = $request->otp;
        $otp = Session::get('user-id-' . $userid);
        if ($otp) {
            if ($otpCur == $otp) {
                $users = DB::table('ml_accounts')->where('branch_id', $userid)->get();
                foreach ($users as $user) {
                    ResetAccountService::dispatch($user->id)->onQueue('account_reset_action');
                }

                return response()->json([
                    'status' => true, // Mengubah 'success' menjadi true
                    'message' => 'Reset akun sudah berjalan, mohon tunggu beberapa menit kedepan, terimasih!',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Kode OTP tidak valid',
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Kode OTP tidak valid.',
            ], 500);
        }
    }

    public function checkVersionMobile(Request $request)
    {
        $data = MobileVersion::orderBy('id', 'desc')->first();

        $version_db = str_replace('.', '', $data->version);
        $version_app = str_replace('.', '', $request->version);

        if ($version_app >= $version_db) {
            return response()->json([
                'status' => true,
                'message' => 'Aplikasi Anda Sudah Terbaru'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Aplikasi Tidak Bisa Dipakai, Silahkan Update Ke Versi Terbaru'
            ]);
        }
    }
}
