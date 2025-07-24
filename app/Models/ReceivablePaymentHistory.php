<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivablePaymentHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function receivable_from($id)
    {
        $MlCurrentAsset = MlCurrentAsset::where('userid', session('id'))->where('id', $id)->first();

        $MlIncome = MlIncome::where('userid', session('id'))->where('id', $id)->first();

        $MlNonBussinessIncome = MlNonBussinessIncome::where('userid', session('id'))->where('id', $id)->first();

        $MlCapital = MlCapital::where('userid', session('id'))->where('id', $id)->first();

        $data = $MlCapital ?? ($MlCurrentAsset ?? ($MlIncome ?? $MlNonBussinessIncome));

        return $data;
    }

    public function ml_current_asset2($id, $account_code_id)
    {
        if ($account_code_id == 1 || $account_code_id == null) {
            $data = MlCurrentAsset::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 7) {
            $data = MlIncome::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 11) {
            $data = MlNonBussinessIncome::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 6) {
            $data = MlCapital::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        } 
        elseif ($account_code_id == 10) {
            $data = MlAdminGeneralFee::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        }
        elseif ($account_code_id == 9) {
            $data = MlSellingCost::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        }
        elseif ($account_code_id == 2) {
            $data = MlFixedAsset::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;
        } 

        else {


            $data = MlCurrentAsset::where('userid', session('id'))->where('id', $id)->first();
            return $data->name;

       }
    }


    public function ml_current_asset3($id, $account_code_id, $userid)
    {
        if ($account_code_id == 1 || $account_code_id == null) {
            $data = MlCurrentAsset::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 7) {
            $data = MlIncome::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 11) {
            $data = MlNonBussinessIncome::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        } elseif ($account_code_id == 6) {
            $data = MlCapital::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        } 
        elseif ($account_code_id == 10) {
            $data = MlAdminGeneralFee::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        }
        elseif ($account_code_id == 9) {
            $data = MlSellingCost::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        }
        elseif ($account_code_id == 2) {
            $data = MlFixedAsset::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
        } 

        else {

            $data = MlCurrentAsset::where('userid', $userid)->where('id', $id)->first();
            return $data->name;
       }
    }

    public function receivable_from_m($id, $userid)
    {
        $MlCurrentAsset = MlCurrentAsset::where('userid', $userid)->where('id', $id)->first();

        $MlIncome = MlIncome::where('userid', $userid)->where('id', $id)->first();

        $MlNonBussinessIncome = MlNonBussinessIncome::where('userid', $userid)->where('id', $id)->first();

        $MlCapital = MlCapital::where('userid', $userid)->where('id', $id)->first();

        $data = $MlCapital ?? ($MlCurrentAsset ?? ($MlIncome ?? $MlNonBussinessIncome));

        return $data;
    }

    public function ml_current_asset()
    {
        return $this->belongsTo(MlCurrentAsset::class, 'payment_to_id', 'id');
    }
}
