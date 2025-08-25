<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWalletData extends Model
{
    protected $table = 'user_wallet_data';
    protected $fillable = [
        'user_id',
        'wallet_address',
        'currency',
        'amount',
        'meta',
        'bnb',
        'status',
    ];
}
