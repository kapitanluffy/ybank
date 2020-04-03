<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transaction;

class Account extends Model
{
    protected $fillable = ['name', 'balance'];

    public $timestamps = false;

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
