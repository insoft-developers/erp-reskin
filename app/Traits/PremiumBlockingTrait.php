<?php

namespace App\Traits;

use App\Models\Account;
use App\Models\MlUserInformation;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait PremiumBlockingTrait
{
    public function get_premium_validation() {
        $data = DB::table('ml_site_config')->first();
        return $data;
    }
}
