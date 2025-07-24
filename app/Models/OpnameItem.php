<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpnameItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function product():BelongsTo
    {
        return $this->BelongsTo(Product::class, 'product_id', 'id');
    }


    public function inter(): BelongsTo
    {
        return $this->belongsTo(InterProduct::class, 'product_id', 'id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'product_id', 'id');
    }

}
