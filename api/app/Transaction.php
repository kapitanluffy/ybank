<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;

class Transaction extends Model
{
    protected $fillable = ['from', 'to', 'details', 'amount'];

    public $timestamps = false;

    public function sender()
    {
        return $this->belongsTo(Account::class, 'from')->select(['id', 'name']);
    }

    public function recipient()
    {
        return $this->belongsTo(Account::class, 'to')->select(['id', 'name']);
    }
}
