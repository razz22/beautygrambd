<?php
/*
|--------------------------------------------------------------------------
| Install Routes
|--------------------------------------------------------------------------
| This route is responsible for handling the installation process
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::controller(InstallController::class)->group(function () {
    Route::get('/', 'step0');
    Route::get('/step1', 'step1')->name('step1');
    Route::get('/step2', 'step2')->name('step2');
    Route::get('/step3/{error?}', 'step3')->name('step3');
    Route::get('/step4', 'step4')->name('step4');
    Route::get('/step5', 'step5')->name('step5');

    Route::post('database_installation', 'databaseInstallation')->name('install.db');
    Route::get('import_sql', 'importSQL')->name('import_sql');
    Route::get('force-import-sql', 'forceImportSQL')->name('force-import-sql');
    Route::post('system_settings', 'updateSystemSettings')->name('system_settings');
    Route::post('purchase_code', 'updatePurchaseCode')->name('purchase.code');
});