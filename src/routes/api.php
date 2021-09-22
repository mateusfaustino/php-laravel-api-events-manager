<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/events/search/{title}', [EventController::class, 'search']);

Route::group(['middleware'=>['auth:sanctum']], function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::get('/events/{id}/edit', [EventController::class, 'edit']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [EventController::class, 'dashboard']);
});
