<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdAdjustmentInterProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function md_adjustment()
    {
        return $this->belongsTo(MdAdjustment::class, 'adjustment_id', 'id');
    }

    public function md_inter_product()
    {
        return $this->belongsTo(InterProduct::class, 'md_inter_product_id', 'id');
    }
}
