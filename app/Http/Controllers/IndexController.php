<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $blogs = Blog::latest()->paginate(10);
        return view('index', compact('blogs'));
    }

    public function loadMoreBlogs(Request $request){

        $page = $request->page ?? 1;

        $blogs = Blog::paginate(10, ['*'], 'page', $page);

        return response()->json([
            'blogs' => view('partials.index', compact('blogs'))->render(),
            'next_page' => $blogs->nextPageUrl()
        ]);
    }

}
