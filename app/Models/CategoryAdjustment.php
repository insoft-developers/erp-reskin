<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryAdjustment extends Model
{
    use HasFactory;

    protected $table = 'category_adjustments';
    protected $guarded = ['id'];

}
