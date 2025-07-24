<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'penjualan_products';

    public $timestamps = false;

    
    public function penjualan() {
        return $this->belongsTo(Penjualan::class, 'penjualan_id', 'id');
    }

    public function product() {
        return $this->belongsTo(MdProduct::class, 'product_id', 'id');
    }

    public function variant() {
        return $this->hasMany(PenjualanProductVarian::class, 'penjualan_product_id', 'id');
    }
}
