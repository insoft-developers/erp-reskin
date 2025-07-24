<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdProductImage extends Model
{
    use HasFactory;

    protected $table = 'md_product_images';

    public function product()
    {
        return $this->belongsTo(MdProduct::class, 'product_id');
    }
}
