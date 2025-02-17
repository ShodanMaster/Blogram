<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){
        $followersBlogs = Auth::user()
            ->following()  // Get the users the authenticated user is following
            ->with(['followedUser.blogs' => function($query) {
                $query->latest()->take(10);  // Eager load the latest 10 blogs of followed users
            }])
            ->get();
        $blogs = Blog::where('ban', false)->latest()->get();
        return response()->json([
            'status' => 200,
            'message' => 'Data Found',
            'data' => [
                'followersBlogs' => $followersBlogs,
                'blogs' => $blogs,
            ]
        ]);
    }

    public function report(Request $request){
        // dd($request->all());
        // dd($request->commentId);

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try{

            $userId = Auth::user()->id;
            $reason = $request->reason;


            //Blog Report
            if ($request->has('blogId') && !empty($request->blogId)) {

                $report = Report::create([
                    'user_id' => $userId,
                    'reportable_type' => Blog::class,
                    'reportable_id' => $request->blogId,
                    'reason' => $reason,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Blog Reported Successfully',
                ]);

            }
            //User Report
            elseif ($request->has('userId') && !empty($request->userId)) {

                $report = Report::create([
                    'user_id' => $userId,
                    'reportable_type' => User::class,
                    'reportable_id' => $request->userId,
                    'reason' => $reason,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'User Reported Successfully',
                ]);

            }

            //Comment Report
            elseif ($request->has('commentId') && !empty($request->commentId)) {

                $report = Report::create([
                    'user_id' => $userId,
                    'reportable_type' => Comment::class,
                    'reportable_id' => $request->commentId,
                    'reason' => $reason,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Comment Reported Successfully',
                ]);

            }

            return response()->json([
                'status' => 400,
                'message' => 'Invalid report type.',
            ]);

        }catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong!'. $e->getMessage(),
            ]);
        }

    }

    public function searchUsers(Request $request){
        // dd($request->all());
        $users = User::where('name', 'LIKE', '%' . $request->search . '%')
                    ->where('ban', false)
                    ->limit(10)
                    ->get();
        if ($users->isNotEmpty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Users Found',
                'data' => $users
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Users Not Found'
        ]);
    }
}
