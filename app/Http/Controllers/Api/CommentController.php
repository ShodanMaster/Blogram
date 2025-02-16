<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function conversation($id){
        try{

            $blog = Blog::findOrFail($id);
            return response()->json([
                'status' => 200,
                'data' => [
                    'blog' => $blog,
                    'comment' => $blog->comments
                ]
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong! '.$e->getMessage()
            ]);
        }
    }

    public function storeComment(Request $request){
        // dd($request->all());
        $request->validate([
            'blogId' => 'required|integer|exists:blogs,id',
            'comment' => 'required|string',
        ]);

        try{

            $blog = Blog::findOrFail($request->blogId);

            $blog->comments()->create([
                'blog_id' => $blog->id,
                'user_id' => auth()->user()->id,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Commented on Blog',
            ]);
        }catch (Exception $e){
            return response()->json([
                'status' => 500,
                'message' =>  'Something Went Wrong! '.$e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Something Went Wrong! '.$e->getMessage());
        }
    }

    public function deleteComment(Request $request){

        $validated = $request->validate([
            'id' => 'required|integer|exists:blogs,id',
        ]);

        try {
            $comment = Comment::find($request->id);

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

}
