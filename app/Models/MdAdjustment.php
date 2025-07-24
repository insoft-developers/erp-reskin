<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdAdjustment extends Model
{
    use HasFactory;

    protected $table = 'md_adjustments';
    protected $guarded = ['id'];
    
    public $timestamps = false;

    public function md_adjustment_product()
    {
        return $this->hasMany(MdAdjustmentProduct::class, 'adjustment_id', 'id');
    }

    public function md_adjustment_material()
    {
        return $this->hasMany(MdAdjustmentMaterial::class, 'adjustment_id', 'id');
    }

    public function md_adjustment_inter_product()
    {
        return $this->hasMany(MdAdjustmentInterProduct::class, 'adjustment_id', 'id');
    }

    public function category():BelongsTo
    {
        return $this->belongsTo(AdjustmentCategory::class, 'category_adjustment_id','id');
    }
}
