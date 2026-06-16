<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\SponsorController;

Route::apiResource('sponsors', SponsorController::class);
