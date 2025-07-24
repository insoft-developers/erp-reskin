<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdExpenseCategory extends Model
{
    use HasFactory;

    protected $table = "md_expense_category";
    protected $guarded = ['id'];
    public $timestamps = false;

    public function md_expense_category_product()
    {
        return $this->hasMany(MdExpenseCategoryProduct::class, 'expense_category_id', 'id');
    }
}
