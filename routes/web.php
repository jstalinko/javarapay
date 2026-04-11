<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::group(['prefix' => 'dashboard' , 'middleware' => ['auth']] , function(){
    Route::get('/' , [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/project' , [DashboardController::class,'project'])->name('dashboard.project');
    Route::post('/project' , [DashboardController::class,'projectStore'])->name('dashboard.project.store');
    Route::get('/transaction' , [DashboardController::class,'transaction'])->name('dashboard.transaction');
    Route::get('/withdrawal' , [DashboardController::class,'withdrawal'])->name('dashboard.withdrawal');
    Route::get('/bank' , [DashboardController::class,'bank'])->name('dashboard.bank');
    Route::get('/webhook' , [DashboardController::class,'webhook'])->name('dashboard.webhook');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
