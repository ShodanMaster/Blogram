<?php

namespace App\Http\Controllers;

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
        return view('admin.index');
    }

    public function users(){
        $users = User::all();
        return view('admin.users.users', compact('users'));
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

    // public function reports(){
    //     $reports = Report::with('reportable')->latest()->get();
    //     // dd($report);
    //     return view('admin.reports.reports', compact('reports'));
    // }

    public function reports(){
        $reports = Report::with('reportable')->latest()->get();

        return view('admin.reports.reports', compact('reports'));
    }

//     public function getReports(Request $request)
// {
//     if ($request->ajax()) {
//         // Start building the query for reports
//         $reports = Report::with('reportable') // assuming reports have a polymorphic relation 'reportable'
//                          ->latest(); // Order by the latest reports

//         // Apply filters if provided in the request
//         if ($request->has('status') && $request->status) {
//             $reports->where('status', $request->status); // Filter by status
//         }

//         if ($request->has('category') && $request->category) {
//             $reports->where('reportable_type', $request->category); // Filter by category (polymorphic entity type)
//         }

//         if ($request->has('date_from') && $request->has('date_to')) {
//             $reports->whereBetween('created_at', [$request->date_from, $request->date_to]); // Filter by date range
//         }

//         // Select relevant columns (you can also limit to specific columns)
//         $reports = $reports->select('*');

//         // Return data for DataTables with additional columns for actions and checkboxes
//         return DataTables::of($reports)
//             ->addColumn('checkbox', function ($report) {
//                 // Adds a checkbox for each report
//                 return '<input type="checkbox" name="select[]" value="' . $report->id . '">';
//             })
//             ->addColumn('action', function ($report) {
//                 // Add action buttons like view, delete, etc.
//                 $detailsUrl = route('admin.reports.show', $report->id); // Route to view details
//                 return '<a class="btn btn-info" href="' . $detailsUrl . '"><i class="fa fa-eye"></i> View</a>';
//             })
//             ->addColumn('reportable_name', function ($report) {
//                 // Show the name of the related entity based on 'reportable_type'
//                 return $report->reportable ? $report->reportable->name : 'N/A';
//             })
//             ->rawColumns(['checkbox', 'action']) // Ensure raw HTML is rendered in those columns
//             ->addIndexColumn() // Adds a serial number column
//             ->make(true); // Returns the data in the proper format for DataTables
//     }

//     return response()->json(['error' => 'Invalid request']); // If it's not an AJAX request
// }



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
