<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function users(){
        $users = User::all();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'google_id' => $user->google_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'ban' => $user->ban,
                ];
            }),

        ]);
    }

    public function banUnbanUser(Request $request)
    {
        $userId = $request->input('user_id');

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User not found']);
        }


        if ($user->ban) {

            $user->ban = false;
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'unbanned',
                'userId' => $user->id,
                'userStatus' => 'Active'
            ]);
        } else {

            $user->ban = true;
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'banned',
                'userId' => $user->id,
                'userStatus' => 'banned'
            ]);
        }
    }

    public function userProfile($id){
        // dd($id);
        try{
            $user = User::find($id);

            if($user){

                $followingUsers = $user->following()->with('followedUser')->get();
                $followedUsers = $user->followers()->with('followerUser')->get();
                return response()->json([
                    'status' => 200,
                    'message' => 'User Data Found',
                    'data' => [
                        'users' => $user,
                        'followingUsers' => $followingUsers,
                        'followedUsers' => $followedUsers,
                    ],
                ]);
            }
            else{
                return response()->json(['status' => 404, 'message' => 'User not found']);
            }

        }catch(Exception $e){
            return response()->json(['status' => 500, 'message' => 'Something Went Wrong! '.$e->getMessage()]);
        }
    }

    public function blogs(){
        $blogs = Blog::all();
        return response()->json([
            'status' => 200,
            'message' => 'Blogs Found',
            'data' => $blogs,
        ]);
    }

    public function banUnbanBlog(Request $request){

        $blogId = $request->input('blog_id');

        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json(['status' => 404, 'message' => 'blog not found']);
        }


        if ($blog->ban) {

            $blog->ban = false;
            $blog->save();

            return response()->json([
                'status' => 200,
                'message' => 'unbanned',
                'blogId' => $blog->id,
                'blogStatus' => 'Active'
            ]);
        } else {

            $blog->ban = true;
            $blog->save();

            return response()->json([
                'status' => 200,
                'message' => 'banned',
                'blogId' => $blog->id,
                'blogStatus' => 'Banned'
            ]);
        }
    }

    public function conversation($id){
        try{

            $blog = Blog::findOrFail($id);
            return response()->json([
                'status' => 200,
                'message'=> 'Conversation Found',
                'data' => [
                    'blog' => $blog,
                    'comments' => $blog->comments
                ]
            ]);
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

    public function comments(){
        $comments = Comment::all();
        return response()->json([
            'status' => 200,
            'message' => 'comments found',
            'data' => $comments->map(function($comment){
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user' => $comment->user,
                    'blog' => $comment->blog,
                ];

            })
        ]);
    }

    public function deleteComment(Request $request){

        $validated = $request->validate([
            'comment_id' => 'required|string'
        ]);

        try {
            $comment = Comment::find($request->comment_id);

            if ($comment) {
                $comment->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Comment Deleted Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Comment Not Found',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reports(){
        $reports = Report::all();

        return response()->json([
            'status' => 200,
            'message' => 'Reports Found',
            'data' => $reports->map(function($report) {
                $reportableType = match ($report->reportable_type) {
                    'App\Models\User' => 'User',
                    'App\Models\Blog' => 'Blog',
                    default => 'Comment',
                };

                return [
                    'id' => $report->id,
                    'user' => $report->user,
                    'reportableType' => $reportableType,
                    'reportable' => $report->reportable,
                    'reason' => $report->reason,
                    'status' => $report->status,
                ];
            }),

        ]);
    }

    public function handleReport(Request $request, $id){
        $request->validate([
            'status' => 'required|in:pending,resolved,dismissed'
        ]);
        try {
            $report = Report::findOrFail($id);
            $modelClass = $report->reportable_type;
            $modelId = $report->reportable_id;
            $status = $request->status;

            if (!in_array($modelClass, [Blog::class, User::class])) {
                return response()->json(['status' => 400,'message'=> 'Invalid reportable type']);
                // return redirect()->back()->with('error', 'Invalid reportable type');
            }

            $model = $modelClass::find($modelId);

            if (!$model) {
                return response()->json(['status' => 404,'message'=> ucfirst(class_basename($modelClass)) . ' Not Found!']);
            }

            $report->update(['status' => $status]);

            if($status == 'resolved'){
                $model->update(['ban' => true]);
                return response()->json([
                    'status' => 200,
                    'message' => ucfirst(class_basename($modelClass)) . ' Banned!'
                ]);
            }

            return response()->json([
                'status' => 200,
                'message' => ucfirst(class_basename($modelClass)) . ' Report Handled!!'
            ]);

        } catch (Exception $e) {

            Log::error('Error handling report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }

}
