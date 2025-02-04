<?php

namespace App\Http\Controllers;

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

    public function deleteBlog(Request $request){

        // dd($request->all());

        $validated = $request->validate([
            'id' => 'required|string'
        ]);

        try {
            $blog = Blog::find(decrypt($request->id));

            if ($blog) {
                $blog->delete();

                return response()->json([
                    'status' => 200,
                    'message' => 'Blog Deleted Successfully',
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Blog Not Found',
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
