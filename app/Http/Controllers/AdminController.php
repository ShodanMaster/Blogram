<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function users(){

        return view('admin.users.users');
    }

    public function getUsers(Request $request)
    {
        $users = User::all();
        if ($request->ajax()) {
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    // Fix the link generation in the name column
                    $user = route('admin.userprofile', encrypt($row->id));
                    return '<a href="' . $user . '" target="_blank">' . $row->name . '</a>';
                })
                ->addColumn('Registration Through', function ($row) {
                    return $row->google_id == null ? 'Registration' : 'Google';
                })
                ->addColumn('action', function ($row) {
                    $statusClass = $row->ban ? 'bg-danger' : 'bg-success';
                    $statusText = $row->ban ? 'Banned' : 'Active';
                    $buttonClass = $row->ban ? 'btn-success' : 'btn-danger';
                    $buttonText = $row->ban ? 'Unban' : 'Ban';

                    return '
                        <div class="user-card">
                            <span id="userStatus' . $row->id . '" class="badge ' . $statusClass . '">
                                ' . $statusText . '
                            </span>

                            <button type="button" class="btn ' . $buttonClass . ' btn-sm" id="banButton" value="' . $row->id . '">
                                ' . $buttonText . '
                            </button>
                        </div>
                    ';
                })
                ->make(true);
        }
    }


    public function banUnbanUser(Request $request)
    {
        $userId = $request->input('user_id');

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }


        if ($user->ban) {

            $user->ban = false;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'unbanned',
                'userId' => $user->id,
                'userStatus' => 'Active'
            ]);
        } else {

            $user->ban = true;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'banned',
                'userId' => $user->id,
                'userStatus' => 'Banned'
            ]);
        }
    }

    public function userProfile($id){
        // dd($id);
        try{
            $user = User::find(decrypt($id));

            if($user){

                return view('admin.users.userprofile', compact('user'));
            }
            else{
                return redirect()->back()->with('error', 'User Not Found');
            }

        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong! '. $e->getMessage());
        }
    }

    public function banUnbanBlog(Request $request)
    {
        $blogId = $request->input('blog_id');

        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json(['status' => 'error', 'message' => 'blog not found']);
        }


        if ($blog->ban) {

            $blog->ban = false;
            $blog->save();

            return response()->json([
                'status' => 'success',
                'message' => 'unbanned',
                'blogId' => $blog->id,
                'blogStatus' => 'Active'
            ]);
        } else {

            $blog->ban = true;
            $blog->save();

            return response()->json([
                'status' => 'success',
                'message' => 'banned',
                'blogId' => $blog->id,
                'blogStatus' => 'Banned'
            ]);
        }
    }

    public function conversation($id){
        try{

            $blog = Blog::findOrFail(decrypt($id));
            return view('admin.users.blog', compact('blog'));
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong!');
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

    public function blogs(){
        $blogs = Blog::all();
        return view('admin.blogs.blogs', compact('blogs'));
    }

    public function comments(){
        $comments = Comment::all();
        return view('admin.comments.comments', compact('comments'));
    }

    public function reports(){
        // $reports = Report::with('reportable')->latest()->get();

        return view('admin.reports.reports');
    }

    public function getReports(Request $request) {
        $reports = Report::with('user', 'reportable')->get();

        if ($request->ajax()) {
            return DataTables::of($reports)
                ->addIndexColumn()
                ->addColumn('reported', function ($row) {

                    $user = route('admin.userprofile', encrypt($row->user->id));
                    return '<a href ="' . $user .'" target="_blank">' . $row->user->name . '</a>';
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('content_type', function ($row) {
                    return class_basename($row->reportable_type);
                })
                ->addColumn('content', function ($row) {
                    if ($row->reportable_type === 'App\Models\Blog') {

                        $blog = route('admin.converstaions', encrypt($row->reportable_id));

                        return '<a href="'.$blog.'" target="_blank">'. $row->reportable->user->name .'</a>';

                    }
                    elseif ($row->reportable_type === 'App\Models\User') {
                        $user = route('admin.userprofile', encrypt($row->user_id));
                        return "<a href=' . $user . ' target='_blank'>" . $row->reportable->name . "</a>";
                    } elseif ($row->reportable_type === 'App\Models\Comment') {
                        if ($row->reportable && !$row->reportable->comment) {
                            return '<span class="text-danger">Comment Was Removed</span>';
                        } elseif ($row->reportable) {
                            return Str::limit($row->reportable->comment, 50);
                        } else {
                            return '<span class="text-danger">No content available</span>';
                        }
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $actionButton = '';

                    if ($row->reportable_type === 'App\Models\Comment') {
                        if ($row->reportable && !$row->reportable->comment) {
                            $actionButton = '<button class="btn btn-success btn-sm">Removed Comment</button>';
                        } elseif ($row->reportable) {
                            $actionButton = '<button type="button" class="btn btn-danger btn-sm" id="deleteComment" data-id="' . encrypt($row->reportable_id) . '">Delete comment</button>';
                        } else {
                            $actionButton = '<span class="text-danger">Report Handled</span>';
                        }
                    } else {
                        $actionButton = '<a href="' . route('admin.handlereport', encrypt($row->id)) . '" class="btn btn-warning btn-sm">Handle Report</a>';
                    }

                    return $actionButton;
                })
                ->make(true);
        }
    }


    public function handleReport($id){
        $report = Report::find(decrypt($id));
        return view('admin.reports.handlereport', compact('report'));
    }

    public function reportHandled(Request $request, Report $report){
        try {

            $modelClass = $report->reportable_type;
            $modelId = $report->reportable_id;
            $status = $request->status;

            if (!in_array($modelClass, [Blog::class, User::class])) {
                return redirect()->back()->with('error', 'Invalid reportable type');
            }

            $model = $modelClass::find($modelId);

            if (!$model) {
                return redirect()->back()->with('error', ucfirst(class_basename($modelClass)) . ' Not Found!');
            }

            $model->update(['ban' => true]);

            $report->update(['status' => $status]);

            return redirect()->back()->with('success', ucfirst(class_basename($modelClass)) . ' Banned!');
        } catch (Exception $e) {

            Log::error('Error handling report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }


}
