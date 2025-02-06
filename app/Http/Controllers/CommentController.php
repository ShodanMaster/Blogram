<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Exception;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function conversation($id){
        try{

            $blog = Blog::findOrFail(decrypt($id));
            return view('blog', compact('blog'));
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

    public function storeComment(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'blogId' => 'required|string',
            'comment' => 'required|string',
        ]);

        try{

            $blog = Blog::findOrFail(decrypt($request->blogId));

            $blog->comments()->create([
                'blog_id' => $blog->id,
                'user_id' => auth()->user()->id,
                'comment' => $request->comment,
            ]);

            return redirect()->back()->with('success', 'Commented');
        }catch (Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong! '.$e->getMessage());
        }
    }

    public function deleteComment(Request $request){

        $validated = $request->validate([
            'id' => 'required|string'
        ]);

        try {
            $comment = Comment::find(decrypt($request->id));

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
