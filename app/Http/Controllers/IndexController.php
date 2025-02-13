<?php

namespace App\Http\Controllers;

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
        $blogs = Blog::where('ban', false)->latest()->paginate(10);
        return view('index', compact('blogs', 'followersBlogs'));
    }

    public function loadMoreBlogs(Request $request){

        $page = $request->page ?? 1;

        $blogs = Blog::paginate(10, ['*'], 'page', $page);

        return response()->json([
            'blogs' => view('partials.index', compact('blogs'))->render(),
            'next_page' => $blogs->nextPageUrl()
        ]);
    }

    public function report(Request $request){
        // dd($request->all());
        // dd(decrypt($request->commentId));

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
                    'reportable_id' => decrypt($request->blogId),
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
                    'reportable_id' => decrypt($request->userId),
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
                    'reportable_id' => decrypt($request->commentId),
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

}
