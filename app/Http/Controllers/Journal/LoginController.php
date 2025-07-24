<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmailForgotPassword;
use App\Jobs\SendWhatsAppMessageForgotPassword;
use App\Models\Account;
use App\Models\UserActivityLogs;
use App\Traits\LogUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use LogUserTrait;

    public function lupaPassword(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required|email|exists:ml_accounts,email',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $pesan = $validator->errors();
            $pesanarr = explode(',', $pesan);
            $find = ['[', ']', '{', '}'];
            $html = '';
            foreach ($pesanarr as $p) {
                $n = str_replace($find, '', $p);
                $o = strstr($n, ':', false);
                $html .= str_replace(':', '', $o);
            }

            return response()->json([
                'success' => false,
                'message' => $html,
            ]);
        }

        try {
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

            $accounts = DB::table('ml_accounts')
                ->where('email', $input['email'])
                ->first();
            $fullname = $accounts->fullname;
            // $accounts_detail = DB::table("ml_user_information")->where("user_id", $accounts->id)->first();
            // $phone_number = $accounts->phone_number;
            $phone_number = $accounts->phone;

            if (env('MAIL_ACTIVE') == 'true') {
                // Panggil job untuk mengirim email verifikasi
                SendVerificationEmailForgotPassword::dispatch($input['email'], $fullname, $tokenVerif)->onQueue('forgot_password');
            }

            // Panggil job untuk mengirim pesan WhatsApp
            SendWhatsAppMessageForgotPassword::dispatch($input['email'], $phone_number, $fullname, $tokenVerif)->onQueue('forgot_password');

            // return redirect('/informasi_forgot_password');
            return response()->json([
                'success' => true,
                'message' => 'Link konfirmasi sudah kami kirim ke Email/No WA anda, silahkan klik link tersebut untuk mengganti kata sandi',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ]);
        }

        try {
            $credentials = $request->only('password');
            $users = Account::where('email', $request['email'])
                ->orWhere('username', $request['email'])
                ->orWhere('phone', $request['email']);

            if ($users->count() > 0) {
                $user = $users->first();
                if ($user->is_active !== 1) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akun anda belum aktif, Silahkan melakukan aktifasi terlebih dahulu.',
                    ]);
                }

                if ($user && Hash::check($credentials['password'], $user->password)) {
                    Auth::login($user);
                    $token = $user->createToken('API Token')->accessToken;

                    if (config('auth.must_verify_email') && !$user->hasVerifiedEmail()) {

                        return response()->json([
                            'success' => false,
                            'message' => 'Email belum terverifikasi.',
                        ]);
                    }

                    $this->insert_user_log($user->id, "login app akuntansi");

                    return response([
                        'success' => true,
                        'message' => 'Login success.',
                        'token' => $token,
                        'data' => $user,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'username atau password masih salah',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'akun anda tidak terdaftar',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function list()
    {
        return response()->json([
            'success' => true,
            'data' => 'good',
        ]);
    }
}
