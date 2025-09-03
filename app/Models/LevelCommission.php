<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LevelCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'level_name',
        'min_invest',
        'direct_referral',
        'commission',
    ];
}
