<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdProduct extends Model
{
    use HasFactory;

    protected $table = 'md_products';

    public $timestamps = false;

    public function landing_page()
    {
        return $this->hasMany(LandingPage::class, 'product_id', 'id');
    }

    public function product_images()
    {
        return $this->hasMany(MdProductImage::class, 'product_id', 'id');
    }

    public function penjualanProduct()
    {
        return $this->hasMany(PenjualanProduct::class, 'product_id', 'id');
    }
}
