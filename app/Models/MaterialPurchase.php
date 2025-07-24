<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialPurchase extends Model
{
    use HasFactory;

    protected $table = 'ml_material_purchases';
    protected $fillable = [
        "transaction_date",
        "userid",
        "account_id",
        "product_count",
        "tax",
        "discount",
        "other_expense",
        "total_purchase",
        "payment_type",
        "supplier_id",
        "reference",
        "image"
    ];

    public function material_purchase_item(): HasMany
    {
       return $this->hasMany(MaterialPurchaseItem::class, 'purchase_id', 'id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
}
