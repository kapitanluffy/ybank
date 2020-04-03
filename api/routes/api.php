<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    $account = DB::table('accounts')->find($id);

    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    $account = (array) $account;

    return $account;
});

Route::get('accounts/{id}/transactions', function ($id) {
    $account = DB::table('accounts')->find($id);

    if (!$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    $account = DB::table('transactions')
             ->whereRaw("`from`=$id OR `to`=$id")
             ->get();

    return $account;
});

Route::post('accounts/{id}/transactions', function (Request $request, $id) {
    $to = $request->input('to');
    $amount = $request->input('amount');
    $details = $request->input('details');

    $recipient = DB::table('accounts')->find($to);
    $account = DB::table('accounts')->find($id);
    $update = 0;

    if (!$recipient || !$account) {
        return response()->json(['error' => 'Account not found'], 404);
    }

    DB::table('accounts')
        ->where("id", "=", $to)
        ->update(['balance' => DB::raw('balance+' . $amount)]);

    DB::table('accounts')
        ->where("id", "=", $id)
        ->update(['balance' => DB::raw('balance-' . $amount)]);

    DB::table('transactions')->insert([
        'from' => $id,
        'to' => $to,
        'amount' => $amount,
        'details' => $details
    ]);
});

Route::get('currencies', function () {
    $account = DB::table('currencies')
              ->get();

    return $account;
});
