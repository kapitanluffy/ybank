<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transaction;
use App\Currency;

class Account extends Model
{
    protected $fillable = ['name', 'balance'];

    public $timestamps = false;

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
