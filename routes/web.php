<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
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

Route::middleware('checkuser')->group(function(){
    Route::get('/',[IndexController::class, 'index'])->name('index');
    Route::get('/load-more-blogs', [IndexController::class, 'loadMoreBlogs'])->name('loadMoreBlogs');

    Route::get('change-password', [LoginController::class, 'changePassword'])->name('changepassword');
    Route::post('password-change', [LoginController::class, 'passwordChange'])->name('passwordchange');
    Route::get('logging-out', [LoginController::class, 'loggingOut'])->name('loggingout');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('', [ProfileController::class, 'index'])->name('index');
        Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('updateprofile');

        Route::get('user-profile/{id}', [ProfileController::class, 'userProfile'])->name('userprofile');

    });

    Route::prefix('blog')->name('blog.')->group(function () {
        Route::post('store-blog', [BlogController::class, 'storeBlog'])->name('storeblog');
        Route::post('update-blog', [BlogController::class, 'updateBlog'])->name('updateblog');
        Route::post('delete-blog', [BlogController::class, 'deleteBlog'])->name('deleteblog');

        Route::post('like-blog', [BlogController::class, 'likeBlog'])->name('likeblog');

    });

    Route::prefix('conversation')->name('conversation.')->group(function(){
        Route::get('/{id}', [CommentController::class, 'conversation'])->name('converstaions');
        Route::post('store-comment', [CommentController::class, 'storeComment'])->name('storecomment');
        Route::post('delete-comment', [CommentController::class, 'deleteComment'])->name('deletecomment');
    });

    Route::post('/follow/{userId}', [FollowController::class, 'follow'])->name('follow');
    Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow'])->name('unfollow');

    Route::get('/search-users', [IndexController::class, 'searchUsers'])->name('searchusers');
    Route::post('report', [IndexController::class, 'report'])->name('report');
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::get('get-users', [AdminController::class, 'getUsers'])->name('getusers');
    Route::get('user-profile/{id}', [AdminController::class, 'userProfile'])->name('userprofile');
    Route::post('/user/ban-unban', [AdminController::class, 'banUnbanUser'])->name('banunbanuser');
    Route::get('blogs', [AdminController::class, 'blogs'])->name('blogs');
    Route::post('/blog/ban-unban', [AdminController::class, 'banUnbanBlog'])->name('banunbanblog');
    Route::get('/conversation/{id}', [AdminController::class, 'conversation'])->name('converstaions');
    Route::get('comments', [AdminController::class, 'comments'])->name('comments');
    Route::get('get-comments', [AdminController::class, 'getComments'])->name('getcomments');
    Route::post('delete-comment', [AdminController::class, 'deleteComment'])->name('deletecomment');

    Route::get('reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('get-reports', [AdminController::class, 'getReports'])->name('getreports');

    Route::get('handle-report/{id}', [AdminController::class, 'handleReport'])->name('handlereport');
    Route::post('report-handled/{report}', [AdminController::class, 'reportHandled'])->name('reporthandled');

});


Route::get('restricted', function(){
    return view('restricted');
})->name('restricted');
