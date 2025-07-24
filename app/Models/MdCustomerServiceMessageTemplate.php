<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdCustomerServiceMessageTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['template_order_in', 'template_struk_out', 'cs_id'];

    public function cs()
    {
        return $this->belongsTo(MdCustomerService::class, 'id', 'cs_id');
    }

    public function msg_template()
    {
        return $this->belongsTo(MdMessageTemplate::class, 'id', 'msg_template_id');
    }
}
