<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
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

    public function receivable_payment_history()
    {
        return $this->hasMany(ReceivablePaymentHistory::class, 'receivable_id', 'id');
    }

    public function balance()
    {
        return $this->amount - $this->receivable_payment_history()->sum('amount');
    }

    public function receivable_from($id)
    {
        $MlCurrentAsset = MlCurrentAsset::where('userid', userOwnerId())
                        ->where('id', $id)
                        ->first();

        $MlIncome = MlIncome::where('userid', userOwnerId())
                        ->where('id', $id)
                        ->first();
                        
        $MlNonBussinessIncome = MlNonBussinessIncome::where('userid', userOwnerId())
                        ->where('id', $id)
                        ->first();

        $MlCapital = MlCapital::where('userid', userOwnerId())
                        ->where('id', $id)
                        ->first();

        $data = $MlCapital ?? $MlCurrentAsset ?? $MlIncome ?? $MlNonBussinessIncome;

        return $data;
    }

    public function receivable_from_m($id, $userid)
    {
        $MlCurrentAsset = MlCurrentAsset::where('userid', $userid)
                        ->where('id', $id)
                        ->first();

        $MlIncome = MlIncome::where('userid', $userid)
                        ->where('id', $id)
                        ->first();
                        
        $MlNonBussinessIncome = MlNonBussinessIncome::where('userid', $userid)
                        ->where('id', $id)
                        ->first();

        $MlCapital = MlCapital::where('userid', $userid)
                        ->where('id', $id)
                        ->first();

        $data = $MlCapital ?? $MlCurrentAsset ?? $MlIncome ?? $MlNonBussinessIncome;

        return $data;
    }
}
