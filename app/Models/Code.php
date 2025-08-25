<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    //

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function owner(){
        return $this->belongsTo(User::class,'code_owner');
    }

    function generateCode(int $length = 10): string
    {
        // Confusing chars বাদ: 0 O 1 l I
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $maxIndex = strlen($alphabet) - 1;

        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $maxIndex)];
        }
        return $code;
    }

}
