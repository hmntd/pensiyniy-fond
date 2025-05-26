<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('pensions', App\Http\Controllers\PensionController::class);
Route::apiResource('clients', App\Http\Controllers\ClientController::class);
Route::apiResource('salaries', App\Http\Controllers\SalaryController::class);
Route::apiResource('benefits', App\Http\Controllers\BenefitController::class);
Route::apiResource('users', App\Http\Controllers\UserController::class);
Route::get('roles', [App\Http\Controllers\UserController::class, 'roles']);

Route::post('salaries/destroy', [App\Http\Controllers\SalaryController::class, 'destroyAll']);
Route::post('/client/benefits', [App\Http\Controllers\BenefitController::class, 'addToUser']);
Route::post('/document/upload', [App\Http\Controllers\DocumentController::class, 'upload']);