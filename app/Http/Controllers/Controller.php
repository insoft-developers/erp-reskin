<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Closure;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function atomic(Closure $callback)
    {
        return DB::transaction($callback);
    }

    public function get_owner_id(int $user_id)
    {
        $user = Account::where('id', $user_id)->first();
        if ($user->role_code === 'general_member') {
            return $user->id;
        } else if ($user->branch_id !== null) {
            $branch = Branch::where('id', $user->branch_id)->first();
            return $branch->account_id;
        }
    }

    public function get_branch_id(int $user_id)
    {
        $user = Account::where('id', $user_id)->first();
        if ($user->role_code === 'staff') {
            return $user->branch_id;
        } else {
            return $user->id;
        }
    }

    function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    function generateRandomString($length = 24, $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $characters = $chars;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
