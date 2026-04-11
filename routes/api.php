<?php

use App\Http\Services\PakasirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/create-payment', function () {
    $pakasir = new PakasirService();

    $tx = $pakasir->createTransaction('qris', 'XX12111XX', 1000);

    return $tx;
});