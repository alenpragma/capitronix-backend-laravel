<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'package';
    protected $fillable = [
        'name',
        'price',
        'interest_rate',
        'duration',
        'return_type',
        'stock',
        'total_sell',
        'active',
        'type',];
    }
