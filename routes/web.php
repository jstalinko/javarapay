<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\AdminController;

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
    Route::post('/withdrawal' , [WithdrawalController::class,'store'])->name('dashboard.withdrawal.store');
    Route::get('/bank' , [DashboardController::class,'bank'])->name('dashboard.bank');

    Route::post('/bank' , [BankController::class,'store'])->name('dashboard.bank.store');

    Route::post('/order' , [OrderController::class,'store'])->name('dashboard.order.store');
    Route::get('/webhook' , [DashboardController::class,'webhook'])->name('dashboard.webhook');

    Route::group(['prefix' => 'admin'], function() {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard.admin');
        Route::get('/project-review', [AdminController::class, 'projectsReview'])->name('dashboard.admin.project-review');
        Route::post('/project/{project}/update-status', [AdminController::class, 'updateProjectStatus'])->name('dashboard.admin.project.update-status');
        
        Route::get('/user-management', [AdminController::class, 'usersManagement'])->name('dashboard.admin.user-management');
        Route::post('/users/{user}/update', [AdminController::class, 'updateUser'])->name('dashboard.admin.user.update');
        
        Route::get('/transactions', [AdminController::class, 'transactionsManagement'])->name('dashboard.admin.transactions');
        
        Route::get('/withdrawal', [AdminController::class, 'withdrawalsManagement'])->name('dashboard.admin.withdrawal');
        Route::post('/withdrawal/{withdrawal}/update', [AdminController::class, 'updateWithdrawalStatus'])->name('dashboard.admin.withdrawal.update');
        
        Route::get('/announcements', [AdminController::class, 'announcementsManagement'])->name('dashboard.admin.announcements');
        Route::post('/announcements', [AdminController::class, 'storeAnnouncement'])->name('dashboard.admin.announcement.store');
        Route::post('/announcements/{announcement}/update', [AdminController::class, 'updateAnnouncement'])->name('dashboard.admin.announcement.update');
        Route::delete('/announcements/{announcement}', [AdminController::class, 'deleteAnnouncement'])->name('dashboard.admin.announcement.delete');
    })->middleware('admin');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
