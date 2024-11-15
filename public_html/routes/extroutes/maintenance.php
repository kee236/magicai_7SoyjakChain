<?php

use App\Http\Controllers\Common\MaintenanceController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {
    Route::prefix('dashboard/admin/settings')
        ->middleware(['auth', 'admin'])
        ->as('dashboard.admin.settings.maintenance.')
        ->controller(MaintenanceController::class)
        ->group(function () {
            Route::get('maintenance', 'index')->name('index');
            Route::post('maintenance', 'update')->name('update');
        });
});
