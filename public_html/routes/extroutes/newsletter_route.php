<?php

use App\Http\Controllers\NewsLetterController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::get('newsletter/new', [NewsLetterController::class, 'new'])->name('newsletter.new');
    Route::post('newsletter/store', [NewsLetterController::class, 'store'])->name('newsletter.store');
});
