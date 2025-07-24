<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPage extends Model
{
    use HasFactory;
    protected $table = 'landing_pages';

    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'slug',
        'script_header',
        'script_header_payment_page',
        'script_header_wa_page',
        'with_customer_name',
        'with_customer_wa_number',
        'with_customer_email',
        'with_customer_full_address',
        'with_customer_proty',
        'html_code',
        'css_code',
        'last_update_content_at',
        'text_submit_button'
    ];

    public function user()
    {
        return $this->belongsTo(MlAccount::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(MdProduct::class, 'product_id');
    }

    public function bump_products()
    {
        return $this->hasMany(LandingPageDetailBumpProduct::class, 'landing_page_id');
    }
}
