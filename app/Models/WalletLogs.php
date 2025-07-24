<?php

namespace App\Models;

use App\Models\MlAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletLogs extends Model
{
    use HasFactory;
    protected $table = 'wallet_logs';
    protected $fillable = [
        'user_id',
        'amount',
        'note',
        'type',
        'from',
        'status',
        'payment_at',
        'group',
        'reference',
        'publisherOrderId',
        'payment_return_url',
    ];

    public function mlAccount()
    {
        return $this->belongsTo(MlAccount::class, 'user_id', 'id');
    }
}
