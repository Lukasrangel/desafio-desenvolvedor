<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UploadController;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


Route::prefix('v1')->group(function() {

    //upload de arquivo csv ou xlsx
    Route::post('/upload',[UploadController::class, 'store']);
    

});

