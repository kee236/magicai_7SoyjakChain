<?php

use App\Http\Controllers\Finance\PaymentProcessController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::match(['get', 'post'], '/{gateway}', [PaymentProcessController::class, 'handleWebhook']);
    });
});
