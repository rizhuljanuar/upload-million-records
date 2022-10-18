<?php

use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/upload', [UploadController::class, 'index']);
Route::get('/progress', [UploadController::class, 'progress']);

Route::post('/upload/file', [UploadController::class, 'upload'])->name('processFile');
Route::get('/progress/data', [UploadController::class, 'csvStoreProcess'])->name('csvStoreProcess');
