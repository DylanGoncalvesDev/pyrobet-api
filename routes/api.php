<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/competitions', [AuthController::class, 'index']);

});

Route::middleware(['auth:api', 'admin'])->group(function () {

   Route::post('/competitions', [AuthController::class, 'store']);
   
});