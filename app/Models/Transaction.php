<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id', 'transactionRef', 'amount', 'transactionStatus'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transactionStatus' => 'array',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * utility method to generate transactionRef
     */
    public function generateTransactionRef($length = 24, $allLower = false, $isTest = false) {
        $lChars = range('a','z');
        $uChars = range('A', 'Z');
        $numbers = range(0,9);
        $chars = implode('', array_merge($lChars, $numbers,$uChars));
        $len = strlen($chars);
        $str = "APEX-";
        $rem = $length - strlen($str);
        for ($i=1; $i<=$rem; $i++){$str .=  $chars[mt_rand(0,$len-1)];}
        return ($allLower) ? strtolower($str) : $str;
    }
}
