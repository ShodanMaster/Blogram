<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function storeBlog(Request $request){
        // dd($request->all());

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string'
        ]);

        try{
            Blog::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Blog Created Succsfully!',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateBlog(Request $request){
        // dd($request->all());
        $request->validate([
            'id' => 'required|integer|exists:blogs,id',
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        try{
            $blog = Blog::find($request->id);

            if ($blog && Auth::user()->id == $blog->user_id) {
                $blog->update([
                    'user_id' => Auth::user()->id,
                    'title' => $request->title,
                    'content' => $request->content,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Blog Updated Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Blog Not Found or Unauthorized',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteBlog(Request $request){

        $validated = $request->validate([
            'id' => ''
        ]);

        try {
            $blog = Blog::find($request->id);

            if ($blog && Auth::user()->id == $blog->user_id) {
                $blog->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Blog Deleted Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Blog Not Found or Unauthorized',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function likeBlog(Request $request){
        $request->validate([
            'blog_id' => 'required|integer|exists:blogs,id',
        ]);

        $blogId = $request->blog_id;
        $blog = Blog::find($blogId);

        if ($blog) {
            $user = auth()->user();  // Get the authenticated user

            // Check if the user has already liked the blog
            if ($user->likedBlogs()->where('blog_id', $blogId)->exists()) {
                // If already liked, remove the like
                $user->likedBlogs()->detach($blogId);
                $status = 'removed';  // The like was removed
            } else {
                // If not liked, add the like
                $user->likedBlogs()->attach($blogId);
                $status = 'added';  // The like was added
            }

            // Get the updated like count (number of users who liked this blog)
            $likeCount = $blog->likedUsers()->count();
            // dd($likeCount);
            // Return the response with the updated like count and status
            return response()->json([
                'status' => 'success',
                'message' => $status,
                'blogId' => $blog->id,
                'likeCount' => $likeCount,  // Updated like count
            ]);
        }

        // If blog is not found
        return response()->json([
            'status' => 'error',
            'message' => 'Blog not found.',
        ]);
    }
}
