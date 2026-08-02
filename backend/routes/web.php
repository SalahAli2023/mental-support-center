<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicStorageController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Public media route
 *
 * نستخدم /media بدلاً من /storage لتجنب أي قواعد أمان في الخادم
 * التي قد تمنع الوصول المباشر لمسار /storage
 */
Route::get('/media/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');
