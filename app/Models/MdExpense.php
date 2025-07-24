<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdExpense extends Model
{
    use HasFactory;

    protected $table = "md_expense";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function md_expense_category()
    {
        return $this->belongsTo(MdExpenseCategory::class, 'expense_category_id', 'id');
    }

    // DARI
    public function from()
    {
        return $this->belongsTo(MlCurrentAsset::class, 'dari', 'id');
    }

    // UNTUK
    public function to($untuk)
    {
        $ml_admin_general_fee = MlAdminGeneralFee::orderBy('name', 'desc')
                    ->where('userid', session('id'))
                    ->where('id', $untuk)
                    ->first();

        $ml_non_business_expense = MlNonBusinessExpense::orderBy('name', 'desc')
                    ->where('userid', session('id'))
                    ->where('id', $untuk)
                    ->first();

        $ml_selling_cost = MlSellingCost::orderBy('name', 'desc')
                    ->where('userid', session('id'))
                    ->where('id', $untuk)
                    ->first();

        $data = $ml_admin_general_fee ?? $ml_non_business_expense ?? $ml_selling_cost;

        return $data;
    }


    public function to_api($untuk, $userid)
    {
        $ml_admin_general_fee = MlAdminGeneralFee::orderBy('name', 'desc')
                    ->where('userid', $userid)
                    ->where('id', $untuk)
                    ->first();

        $ml_non_business_expense = MlNonBusinessExpense::orderBy('name', 'desc')
                    ->where('userid', $userid)
                    ->where('id', $untuk)
                    ->first();

        $ml_selling_cost = MlSellingCost::orderBy('name', 'desc')
                    ->where('userid', $userid)
                    ->where('id', $untuk)
                    ->first();

        $data = $ml_admin_general_fee ?? $ml_non_business_expense ?? $ml_selling_cost;

        return $data;
    }
}
