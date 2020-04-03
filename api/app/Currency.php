<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Account;

class Currency extends Model
{
    protected $fillable = ['name', 'symbol'];
}
