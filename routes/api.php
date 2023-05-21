<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\SubscribeController;
use \App\Http\Controllers\PaymentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('subscribes', [SubscribeController::class, 'getSubscribes'])
        ->name('subscribes.getSubscribes');
    Route::get('subscribes/user', [SubscribeController::class, 'getUserSubscribe'])
        ->name('subscribes.getUserSubscribe');

    Route::post('payment', [PaymentsController::class, 'payment'])
        ->name('payment');
    Route::post('payment/success', [PaymentsController::class, 'success'])
        ->name('payment.success');
    Route::post('payment/cancel', [PaymentsController::class, 'cancel'])
        ->name('payment.cancel');
});


