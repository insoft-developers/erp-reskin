<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = "md_product_category";

    protected $fillable = [
        "name",
        "code",
        "created",
        "user_id",
        "image",
        "description"
    ];

    public $timestamps = false;
}
