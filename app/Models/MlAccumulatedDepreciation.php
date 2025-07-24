<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlAccumulatedDepreciation extends Model
{
    use HasFactory;

    protected $table = "ml_accumulated_depreciation";
    protected $guarded = ['id'];
    public $timestamps = false;
}
