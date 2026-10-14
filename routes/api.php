<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\PredictionsController;
use App\Http\Controllers\TeamsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {

  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/competitions', [CompetitionController::class, 'index']);
  Route::get('/teams', [TeamsController::class, 'index']);
  Route::post('/predictions', [PredictionsController::class, 'store']);
  Route::post('/predictions', [PredictionsController::class, 'index']);
  Route::put('/predictions{id}', [PredictionsController::class, 'update']);

});

Route::middleware(['auth:api', 'admin'])->group(function () {

   Route::post('/competitions', [CompetitionController::class, 'store']);
   Route::put('/competitions/{id}', [CompetitionController::class, 'update']);
   Route::delete('/competitions/{id}', [CompetitionController::class, 'destroy']);
   Route::post('/teams', [TeamsController::class, 'store']);
   Route::put('/teams/{id}', [TeamsController::class, 'update']);
   Route::delete('/teams/{id}', [TeamsController::class, 'destroy']);
});