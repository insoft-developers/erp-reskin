<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComposition extends Model
{
    use HasFactory;
    protected $table = "md_product_compositions";




    public function material():BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id','id');
    }

    public function inter():BelongsTo
    {
        return $this->belongsTo(InterProduct::class, 'material_id','id');
    }
}



