<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterProduct extends Model
{
    use HasFactory;

    protected $table = 'md_inter_products';
    protected $fillable = [
        "userid",
        "product_name",
        "sku",
        "category_id",
        "cost",
        "composition",
        "description",
        "stock",
        "unit",
        "min_stock",
        "ideal_stock"
    ];


    public function inter_category(): BelongsTo
    {
        return $this->belongsTo(InterCategory::class, 'category_id', 'id');
    }

    public function inter_compose_product(): HasMany
    {
        return $this->hasMany(InterComposeProduct::class, 'inter_product_id', 'id');
    }

}
