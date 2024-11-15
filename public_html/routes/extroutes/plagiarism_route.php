<?php

use App\Http\Controllers\PlagiarismController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('openai')->name('openai.')->group(function () {
            Route::get('/detectaicontent', [PlagiarismController::class, 'detectAIContent'])->name('detectaicontent.index');
            Route::post('/aicontentcheck', [PlagiarismController::class, 'detectAIContentCheck'])->name('detectaicontent.check');
            Route::post('/aicontentsave', [PlagiarismController::class, 'detectAIContentSave'])->name('detectaicontent.save');
            Route::get('/plagiarism', [PlagiarismController::class, 'plagiarism'])->name('plagiarism.index');
            Route::post('/plagiarismcheck', [PlagiarismController::class, 'plagiarismCheck'])->name('plagiarism.check');
            Route::post('/plagiarismsave', [PlagiarismController::class, 'plagiarismSave'])->name('plagiarism.save');
        });
    });
    Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/plagiarism', [PlagiarismController::class, 'plagiarismSetting'])->name('plagiarism');
            // Route::get('/plagiarism/test', [PlagiarismController::class, 'serperapiTest'])->name('plagiarism.test');
            Route::post('/plagiarismapi-save', [PlagiarismController::class, 'plagiarismSettingSave'])->name('plagiarism.setting.save');
        });
    });
});
