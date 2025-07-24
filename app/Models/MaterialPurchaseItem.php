<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialPurchaseItem extends Model
{
    use HasFactory;
    protected $table = 'ml_material_purchase_items';

    protected $guarded = ['id'];


    public function material(): BelongsTo 
    {
        return $this->belongsTo(Material::class, 'product_id', 'id');
    }
}
