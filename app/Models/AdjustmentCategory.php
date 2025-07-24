<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentCategory extends Model
{
    use HasFactory;

    protected $table = "category_adjustments";

    protected $fillable = [
        "code",
        "name",
        "account_id"
    ];
}
