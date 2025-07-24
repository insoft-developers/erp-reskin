<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlSellingCost extends Model
{
    use HasFactory;

    protected $table = "ml_selling_cost";
    protected $guarded = ['id'];
    public $timestamps = false;
}
