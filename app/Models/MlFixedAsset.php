<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlFixedAsset extends Model
{
    use HasFactory;

    protected $table = "ml_fixed_assets";
    protected $guarded = ['id'];
    public $timestamps = false;
}
