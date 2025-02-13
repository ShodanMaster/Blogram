<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){

        $blogs = Blog::count();
        $users = User::count();
        $comments = Comment::whereHas('blog')->count();
        $reports = Report::count();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'blogs' => $blogs,
                'users' => $users,
                'comments' => $comments,
                'reports' => $reports,
            ],
        ]);
    }
}
