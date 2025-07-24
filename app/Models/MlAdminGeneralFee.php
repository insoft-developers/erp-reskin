<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAdminGeneralFee extends Model
{
    use HasFactory;

    protected $table = "ml_admin_general_fees";
    protected $guarded = ['id'];
    public $timestamps = false;
}
