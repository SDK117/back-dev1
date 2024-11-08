<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('logout-all', [AuthController::class, 'logoutAll']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('search', [AuthController::class, 'search']);
    Route::delete('/user/delete', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');

    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::put('/admin/users/{id}/status', [AdminUserController::class, 'toggleAccountStatus']);

});
