<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductImages;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;
    protected $table = "md_products";
    protected $guarded = ['id'];
    protected $fillable = [
        "category_id",
        "code",
        "sku",
        "barcode",
        "name",
        "price",
        "price_ta",
        "price_mp",
        "price_cus",
        "cost",
        "default_cost",
        "unit",
        "quantity",
        "stock_alert",
        "sell",
        "created",
        "user_id",
        "is_variant",
        "is_manufactured",
        "buffered_stock",
        "weight",
        "description",
        "created_by",
        "is_editable"
    ];

    public $timestamps = false;

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImages::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(ProductImages::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function variant()
    {
        return $this->hasMany(ProductVarian::class, 'product_id', 'id');
    }

    public function composition():HasMany
    {
        return $this->hasMany(ProductComposition::class, 'product_id','id');
    }
}
