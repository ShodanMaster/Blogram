<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProfileController;
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

    Route::get('users', [AdminController::class, 'users']);
    Route::post('/user/ban-unban', [AdminController::class, 'banUnbanUser']);
    Route::get('user-profile/{id}', [AdminController::class, 'userProfile']);

    Route::get('blogs', [AdminController::class, 'blogs']);
    Route::post('/blog/ban-unban', [AdminController::class, 'banUnbanBlog'])->name('banunbanblog');
    Route::get('/conversation/{id}', [AdminController::class, 'conversation']);

    Route::get('comments', [AdminController::class, 'comments']);
    Route::post('delete-comment', [AdminController::class, 'deleteComment']);

    Route::get('reports', [AdminController::class, 'reports']);
    Route::post('handle-report/{id}', [AdminController::class, 'handleReport']);

    Route::get('refresh', [LoginController::class, 'refreshToken']);
    Route::get('logging-out', [LoginController::class, 'loggingOut']);
});

Route::middleware('auth:webapi')->group(function(){
    Route::get('', [IndexController::class, 'index']);
    Route::post('report', [IndexController::class, 'report']);

    Route::post('store-blog', [BlogController::class, 'storeBlog']);
    Route::post('update-blog', [BlogController::class, 'updateBlog']);
    Route::post('delete-blog', [BlogController::class, 'deleteBlog']);
    Route::post('like-blog', [BlogController::class, 'likeBlog']);

    Route::get('conversation/{id}', [CommentController::class, 'conversation']);
    Route::post('store-comment', [CommentController::class, 'storeComment']);
    Route::post('delete-comment', [CommentController::class, 'deleteComment']);

    Route::get('profile', [ProfileController::class, 'index'])->name('index');
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::get('user-profile/{id}', [ProfileController::class, 'userProfile'])->name('userprofile');

    Route::post('/follow-unfollow/{userId}', [FollowController::class, 'toggleFollow']);
    Route::post('search-users', [IndexController::class, 'searchUsers']);

});
