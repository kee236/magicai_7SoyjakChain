<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIWebChatController;

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::prefix('openai')->name('openai.')->group(function () {
            Route::get('/webchat', [AIWebChatController::class, 'openAIGeneratorWorkbook'])->name('webchat.workbook');
            Route::post('/webchat/open-chat-area-container', [AIWebChatController::class, 'openChatAreaContainer']);
            Route::post('/webchat/start-new-chat', [AIWebChatController::class, 'startNewChat']);
            Route::get('/webchat/stream', [AIWebChatController::class, 'chatStream'])->name('webchat.stream');
            Route::match(['get', 'post'], '/webchat/chat-send', [AIWebChatController::class, 'chatOutput']);
        });
    });
});
