<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IntroductionController;

Route::prefix('dashboard')
    ->middleware(['auth', 'admin'])
    ->name('dashboard.')->group(function () {
        Route::resource('introductions', IntroductionController::class)->only(['index', 'store']);
    });
