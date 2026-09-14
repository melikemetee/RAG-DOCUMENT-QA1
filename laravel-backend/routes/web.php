<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/php-test', function () {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ];
});

Route::get('/', [DocumentController::class, 'index']);

Route::get('/sor', function () {
    return view('welcome');
});

Route::post('/sor', [QuestionController::class, 'sor']);

Route::post('/yukle', [UploadController::class, 'yukle']);