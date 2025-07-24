<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarian extends Model
{
    use HasFactory;
    protected $table = "md_product_varians";

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
