<?php

use Illuminate\Support\Facades\Route;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Route::get('/', function () {
    return view('welcome');
});

