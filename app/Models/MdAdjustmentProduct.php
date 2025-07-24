<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdAdjustmentProduct extends Model
{
    use HasFactory;

    protected $table = 'md_adjustment_products';
    protected $guarded = ['id'];

    public $timestamps = false;

    public function md_adjustment()
    {
        return $this->belongsTo(MdAdjustment::class, 'adjustment_id', 'id');
    }

    public function md_product()
    {
        return $this->belongsTo(MdProduct::class, 'product_id', 'id');
    }
}
