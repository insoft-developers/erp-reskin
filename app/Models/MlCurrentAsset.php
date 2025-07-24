<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlCurrentAsset extends Model
{
    use HasFactory;

    protected $table = "ml_current_assets";
    protected $guarded = ['id'];
    public $timestamps = false;
}
