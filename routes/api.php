<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ApisController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\TestController;


Route::get('/apis', [ApisController::class, 'get']);
Route::get('/source', [SourceController::class, 'get']);
Route::get('/news', [NewsController::class, 'get']);

Route::prefix('test')->group(function () {
    Route::post('newsapi', [TestController::class, 'testNewsapi']);
    Route::post('nytimes', [TestController::class, 'testNytimes']);
    Route::post('opennews', [TestController::class, 'testOpennews']);
});
