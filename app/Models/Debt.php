<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function ml_longterm_debt()
    {
        return $this->belongsTo(MlLongtermDebt::class, 'debt_from', 'id');
    }

    public function ml_shortterm_debt()
    {
        return $this->belongsTo(MlShorttermDebt::class, 'debt_from', 'id');
    }

    public function ml_current_asset()
    {
        return $this->belongsTo(MlCurrentAsset::class, 'save_to', 'id');
    }

    public function ml_selling_cost()
    {
        return $this->belongsTo(MlSellingCost::class, 'save_to', 'id');
    }
    public function ml_fixed_asset()
    {
        return $this->belongsTo(MlFixedAsset::class, 'save_to', 'id');
    }


    public function ml_general_fee()
    {
        return $this->belongsTo(MlAdminGeneralFee::class, 'save_to', 'id');
    }

    public function debt_payment_history()
    {
        return $this->hasMany(DebtPaymentHistory::class, 'debt_id', 'id');
    }

    public function balance()
    {
        return $this->amount - $this->debt_payment_history()->sum('amount');
    }
}
