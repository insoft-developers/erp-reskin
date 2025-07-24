<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    protected $table = 'penjualan';

    protected $fillable = [
        'id',
        'cs_id',
        'reference',
        'flag_id',
        'cust_name',
        'cust_phone',
        'cust_kecamatan',
        'cust_kelurahan',
        'cust_alamat',
        'detail',
        'paid',
        'status',
        'payment_method',
        'user_id',
        'diskon',
        'shipping',
        'tax',
        'customer_id',
        'order_total',
        'payment_status',
        'payment_at',
        'note',
        'branch_id',
        'staff_id',
        'flip_ref',
        'qr_codes_id',
        'price_type',
        'created',
        'custom_date',
        'payment_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(MdCustomer::class, 'customer_id');
    }

    public function desk()
    {
        return $this->belongsTo(QrCode::class, 'qr_codes_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(Account::class, 'staff_id');
    }

    public function products()
    {
        return $this->hasMany(PenjualanProduct::class, 'penjualan_id', 'id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function cs()
    {
        return $this->belongsTo(MdCustomerService::class, 'cs_id', 'id');
    }

    // tolong tambahkan fungsi relasi dengan flag_id ke model PaymentMethodFlags
    public function flag()
    {
        return $this->belongsTo(PaymentMethodFlags::class, 'flag_id', 'id');
    }

    public function penjualan_products()
    {
        return $this->hasMany(PenjualanProduct::class, 'penjualan_id', 'id');
    }
}
