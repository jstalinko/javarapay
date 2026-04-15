<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DocController;
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');
Route::get('/payment/{project_id}/{txid}' , [OrderController::class,'invoice'])->name('payment.invoice');
Route::post('/payment/{project_id}/{txid}/pay', [OrderController::class, 'pay'])->name('payment.pay');


Route::group(['prefix' => 'docs'] , function()
{
    Route::get('/',[DocController::class,'index'])->name('docs.index');
});


Route::group(['prefix' => 'dashboard' , 'middleware' => ['auth']] , function(){
    Route::get('/' , [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/project' , [DashboardController::class,'project'])->name('dashboard.project');
    Route::post('/project' , [ProjectController::class,'store'])->name('dashboard.project.store');
    Route::post('/project/{project}' , [ProjectController::class,'update'])->name('dashboard.project.update');
    Route::get('/project/{id}/channels',[ProjectController::class,'channels'])->name('dashboard.project.channels');
    Route::post('/project/{project}/channels',[ProjectController::class,'updateChannels'])->name('dashboard.project.channels.update');

    Route::post('/project/{project}/toggle-production' , [ProjectController::class,'toggleProduction'])->name('dashboard.project.toggle_production');
    Route::get('/transaction' , [DashboardController::class,'transaction'])->name('dashboard.transaction');
    Route::get('/withdrawal' , [DashboardController::class,'withdrawal'])->name('dashboard.withdrawal');
    Route::get('/bank' , [DashboardController::class,'bank'])->name('dashboard.bank');

    Route::post('/bank' , [BankController::class,'store'])->name('dashboard.bank.store');

    Route::post('/order' , [OrderController::class,'store'])->name('dashboard.order.store');
    Route::get('/webhook' , [DashboardController::class,'webhook'])->name('dashboard.webhook');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
