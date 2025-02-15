<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('logging-in', [LoginController::class, 'loggingIn']);
Route::post('register', [LoginController::class, 'register']);

Route::middleware('auth:adminapi')->prefix('admin')->group(function(){
    Route::get('/', [AdminController::class, 'index']);
    Route::get('refresh', [LoginController::class, 'refreshToken']);
    Route::get('logging-out', [LoginController::class, 'loggingOut']);
});
