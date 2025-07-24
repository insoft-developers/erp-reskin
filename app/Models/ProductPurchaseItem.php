<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPurchaseItem extends Model
{
    use HasFactory;
    protected $table = 'ml_product_purchase_items';

    protected $guarded = ['id'];


    public function product(): BelongsTo 
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
