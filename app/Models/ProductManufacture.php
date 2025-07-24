<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductManufacture extends Model
{
    use HasFactory;
    protected $table = 'ml_product_manufactures';
    protected $fillable = [
        "transaction_date",
        "userid",
        "product_id",
        "account_id",
        "quantity",
        "cost",
        "tax",
        "discount",
        "other_expense",
        "total_purchase",
        

    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
