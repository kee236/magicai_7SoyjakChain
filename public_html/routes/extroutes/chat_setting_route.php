<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\Chatbot\ChatbotController;
use App\Http\Controllers\User\Chatbot\ChatbotTrainingController;
use App\Http\Controllers\User\ChatCategoryController;
use App\Http\Controllers\User\ChatTemplateController;

Route::prefix('dashboard')->middleware('auth')->name('dashboard.')->group(function () {
    Route::prefix('user')->name('user.')->group(function () {
        Route::group([
            'as' => 'chat-setting.',
            'prefix' => 'chat-setting',

        ], function () {
            Route::resource('chat-category', ChatCategoryController::class)

                ->except('show', 'destroy');
            Route::get('chat-category/{chat_category}/delete', [ChatCategoryController::class, 'destroy'])
                ->name('chat-category.destroy');

            Route::resource('chat-template', ChatTemplateController::class)
                ->except('show');

            Route::group([
                'as' => 'chatbot.',
                'prefix' => 'chatbot/{chatbot}',
                'controller' => ChatbotTrainingController::class,
            ], function () {
                Route::post('text', 'text')->name('text');
                Route::post('qa', 'qa')->name('qa');

                Route::post('training', 'training')->name('training');
                Route::get('web-sites', 'getWebSites')->name('web-sites');
                Route::post('web-sites', 'postWebSites');
                Route::post('upload-pdf', 'uploadPdf')->name('upload-pdf');
                Route::delete('item/{id}', 'deleteItem')->name('item.delete');
            });

            Route::resource('chatbot', ChatbotController::class);
        });
    });
});
