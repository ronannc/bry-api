<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::apiResource('companies', CompanyController::class);
Route::post('persons/duplicates/update', [PersonController::class, 'updateDuplicates']);
Route::apiResource('persons', PersonController::class);
Route::get('identidades/duplicadas', [PersonController::class, 'duplicadas']);
