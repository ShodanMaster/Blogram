<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){
        $blog = Blog::all();
        // $followersBlogs = Auth::user()
        //     ->following()  // Get the users the authenticated user is following
        //     ->with(['followedUser.blogs' => function($query) {
        //         $query->latest()->take(10);  // Eager load the latest 10 blogs of followed users
        //     }])
        //     ->get();

        $blogs = $blog->map(function($b){
            return[
                'id' => $b->id,
                'title' => $b->title,
                'content' => $b->content,
                'Author' => $b->user->name,
            ];
        });


        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' =>  $blogs,
            
        ]);
    }
}
