<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\TeamsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/competitions', [CompetitionController::class, 'index']);
  Route::get('/teams', [TeamsController::class, 'index']);

});

Route::middleware(['auth:api', 'admin'])->group(function () {

   Route::post('/competitions', [CompetitionController::class, 'store']);
   Route::put('/competitions/{id}', [CompetitionController::class, 'update']);
   Route::delete('/competitions/{id}', [CompetitionController::class, 'destroy']);
   Route::post('/teams', [TeamsController::class, 'store']);
   
});