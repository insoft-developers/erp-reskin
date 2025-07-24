<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAccountInfo extends Model
{
    use HasFactory;

    protected $table = 'ml_account_info';
    protected $guarded = ['id'];
}
