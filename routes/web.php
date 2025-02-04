<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('logging-in', [LoginController::class, 'loggingIn'])->name('loggingin');
Route::post('register-user', [LoginController::class, 'registerUser'])->name('registeruser');

Route::get('auth/google',[LoginController::class, 'googlePage'])->name('auth.google');
Route::get('auth/google/callback',[LoginController::class, 'googleCallBack'])->name('auth.google.callback');

ROute::middleware('auth')->group(function(){
    Route::get('/',[IndexController::class, 'index'])->name('index');
    Route::get('/load-more-blogs', [IndexController::class, 'loadMoreBlogs'])->name('loadMoreBlogs');

    Route::get('change-password', [LoginController::class, 'changePassword'])->name('changepassword');
    Route::post('password-change', [LoginController::class, 'passwordChange'])->name('passwordchange');
    Route::get('logging-out', [LoginController::class, 'loggingOut'])->name('loggingout');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('', [ProfileController::class, 'index'])->name('index');
        Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('updateprofile');
    });

    Route::prefix('blog')->name('blog.')->group(function () {
        Route::post('store-blog', [BlogController::class, 'storeBlog'])->name('storeblog');
        Route::post('delete-blog', [BlogController::class, 'deleteBlog'])->name('deleteblog');
    });
});
