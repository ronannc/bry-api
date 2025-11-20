<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::apiResource('companies', CompanyController::class);
Route::apiResource('persons', PersonController::class);

