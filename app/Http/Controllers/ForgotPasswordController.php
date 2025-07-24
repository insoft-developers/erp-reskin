<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Str;
use App\Jobs\SendMailKeting;
use Illuminate\Http\Request;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendVerificationEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWhatsAppMessageForgotPassword;
use App\Jobs\SendVerificationEmailForgotPassword;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        $view = "login";
        return view("auth.forgot-password-form", compact('view'));
    }

    public function send_token(Request $request)
    {
        $input = $request->all();

        $rules = array(
            "email" => "required|email|exists:ml_accounts,email",
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            // return redirect()->back()->withInput()->withErrors($validator);
            return response()->json([
                'errors' => $validator->messages(),
            ]);
        }

        $tokenVerif = Str::random(64);

        $cek = DB::table('mail_tokens')->where('email', $input['email']);
        if ($cek->count() > 0) {
            DB::table('mail_tokens')->where('email', $input['email'])->update(["token" => $tokenVerif]);
        } else {
            DB::table('mail_tokens')->insert([
                'email' => $input['email'],
                "token" => $tokenVerif
            ]);
        }

        $accounts = DB::table("ml_accounts")->where("email", $input["email"])->first();
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
            'status' => true,
            'message' => 'Link konfirmasi sudah kami kirim ke Email/No WA anda, silahkan klik link tersebut untuk mengganti kata sandi'
        ]);
    }

    public function information()
    {
        $view = "forgot password information";
        return view("auth.forgot-password-information", compact('view'));
    }

    public function reset_password(Request $request)
    {
        $view = "Reset Password";
        $input = $request->all();
        $cek = DB::table('mail_tokens')
            ->where('token', $input['code'] ?? 0);

        if ($cek->count() == 1) {
            $data = $cek->first();
            $email = $data->email ?? null;
            return view("auth.reset_password", compact("email", "view"));
        } else {
            return  Redirect::to('frontend_login')->with('error', "Failed, Token is invalid!");
        }
    }

    public function change_password(Request $request)
    {
        $input = $request->all();

        $rules = array(
            "email" => "required|email",
            "password" => "required|min:6|confirmed"
        );

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $input['password'] = bcrypt($input['password']);
        $query = Account::where("email", $input["email"])->first();
        $query->password = $input['password'];
        $query->save();

        // Redirect atau response sesuai kebutuhan
        return Redirect::to('/frontend_login')
            ->with('success', 'Passsword telah dirubah');
    }
}
