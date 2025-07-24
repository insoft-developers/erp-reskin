<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shrinkage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ml_fixed_asset()
    {
        return $this->belongsTo(MlFixedAsset::class, 'ml_fixed_asset_id', 'id');
    }

    public function ml_accumulated_depreciation()
    {
        return $this->belongsTo(MlAccumulatedDepreciation::class, 'ml_accumulated_depreciation_id', 'id');
    }

    public function ml_admin_general_fee()
    {
        return $this->belongsTo(MlAdminGeneralFee::class, 'ml_admin_general_fee_id', 'id');
    }

    public function shrinkageSimulate()
    {
        return $this->hasMany(ShrinkageSimulate::class, 'shrinkage_id', 'id');
    }
}
