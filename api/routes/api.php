<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Account;
use App\Transaction;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
*/

Route::get('accounts/{id}', function ($id) {
    $account = Account::with('currency')->find($id);

    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    return $account;
});

Route::post('accounts/', function (Request $request) {
    $name = $request->input('name');
    $balance = $request->input('balance');

    $account = ['name' => $name, 'balance' => $balance];
    $account = Account::create($account);

    return $account;
});

Route::get('accounts/{id}/transactions', function ($id) {
    $account = Account::find($id);

    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    $transactions = Transaction::with('recipient', 'sender')
        ->where('from', $id)
        ->orWhere('to', $id)
        ->get();

    return $transactions;
});

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $to = $request->input('to');
    $amount = $request->input('amount');
    $details = $request->input('details');

    $recipient = Account::find($to);
    $account = Account::find($id);

    if (!$recipient || !$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    $credit = ($account->balance - $amount);
    if ($credit < 0) {
        return response()->json(['error' => 'Not enough funds to complete transaction'], 400);
    }

    $debit = ($recipient->balance + $amount);

    $recipient->balance = $debit;
    $recipient->save();

    $account->balance = $credit;
    $account->save();

    $transaction = Transaction::create([
        'from' => $id,
        'to' => $to,
        'amount' => $amount,
        'details' => $details
    ]);

    $transaction = Transaction::with('recipient', 'sender')->find($transaction['id']);

    return $transaction;
});

Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
