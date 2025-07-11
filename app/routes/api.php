<?php

use App\Http\Controllers\api\v1\RecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UploadController;
use App\Http\Controllers\Api\v1\UserController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


Route::prefix('v1')->group(function() {

    //upload de arquivo csv ou xlsx
    Route::post('/upload',[UploadController::class, 'store']);
    
    //listagem histórico de arquivo
    Route::get('/historic', [UploadController::class, 'show']);

    //busca de uploads
    Route::post('/uploads/search', [UploadController::class, 'search']);

    //busca de records no arquivo
    Route::post('/uploads/{safeName}', [RecordController::class,'search']);

});

