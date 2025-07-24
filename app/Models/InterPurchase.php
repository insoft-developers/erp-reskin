<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterPurchase extends Model
{
    use HasFactory;

    protected $table = 'ml_inter_purchases';
    protected $fillable = ['transaction_date', 'userid', 'product_id', 'account_id', 'quantity','cost', 'tax', 'discount', 'other_expense', 'total_purchase'];



    public function inter_product() : BelongsTo 
    {
        return $this->belongsTo(InterProduct::class, 'product_id', 'id');
    }
}
