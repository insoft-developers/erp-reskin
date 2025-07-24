<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdExpenseCategoryProduct extends Model
{
    use HasFactory;

    protected $table = "md_expense_category_products";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function md_expense_category()
    {
        return $this->belongsTo(MdExpenseCategory::class, 'expense_category_id', 'id');
    }

    public function md_product()
    {
        return $this->belongsTo(MdProduct::class, 'product_id', 'id');
    }
}
