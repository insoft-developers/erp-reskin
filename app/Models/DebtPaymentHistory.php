<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPaymentHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function payment_to($type)
    {
        if ($type == 'Utang Jangka Panjang') {
            return MlLongtermDebt::where('id', $this->payment_to_id)->first();
        } elseif ($type == 'Utang Jangka Pendek') {
            return MlShorttermDebt::where('id', $this->payment_to_id)->first();
        }
    }

    public function payment_from()
    {
        return $this->belongsTo(MlCurrentAsset::class, 'payment_from_id', 'id');
    }


    public function payment_fixed()
    {
        return $this->belongsTo(MlFixedAsset::class, 'payment_from_id', 'id');
    }

    public function payment_selling()
    {
        return $this->belongsTo(MlSellingCost::class, 'payment_from_id', 'id');
    }

    public function payment_cogs()
    {
        return $this->belongsTo(MlCostGoodSold::class, 'payment_from_id', 'id');
    }

    public function payment_cost(): BelongsTo
    {
        return $this->belongsTo(MlAdminGeneralFee::class, 'payment_from_id', 'id');
    }

    public function payment_income(): BelongsTo
    {
        return $this->belongsTo(MlNonBussinessIncome::class, 'payment_from_id', 'id');
    }
      
}
