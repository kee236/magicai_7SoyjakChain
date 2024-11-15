<?php

use App\Http\Controllers\SynthesiaController;

Route::prefix('dashboard')
    ->middleware('auth')
    ->name('dashboard.')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::resource('ai-avatar', SynthesiaController::class)->except('destroy');
            Route::get('ai-avatar-delete/{id}', [SynthesiaController::class, 'delete'])->name('ai-avatar.delete');
            Route::get('ai-avatar-check', [SynthesiaController::class, 'checkVideoStatus'])->name('ai-avatar.check');
        });
    });
