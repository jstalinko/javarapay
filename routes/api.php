<?php

use App\Http\Controllers\APITransactionController;
use App\Http\Services\PakasirService;
use App\Http\Services\TripayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/webhook' , WebhookController::class)->name('api.webhook');

Route::get('/create-payment', function () {
    $pakasir = new PakasirService();

    $tx = $pakasir->createTransaction('qris', 'XX12111XX', 1000);

    return $tx;
});

Route::middleware('auth.javarapay')->group(function () {

    Route::group(['prefix' => 'transaction'], function () {
        Route::post('/create', [APITransactionController::class, 'create'])->name('api.tx.create');
        Route::get('/detail/{txid}', [APITransactionController::class, 'detail'])->name('api.tx.detail');
    });

    Route::group(['prefix' => 'channel'], function () {
        Route::get('/', [APITransactionController::class, 'channels'])->name('api.tx.channel');
        Route::get('/{code}', [APITransactionController::class, 'channelDetail'])->name('api.tx.channel.detail');
    });

});

Route::get('/channels' , function(){
    
    $tripay = new TripayService();
    $channels = $tripay->channels();

    return $channels;
});