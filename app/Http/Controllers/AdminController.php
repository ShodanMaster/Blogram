<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function users(){
        $users = User::all();
        return view('admin.users.users', compact('users'));
    }

    public function banUnban(Request $request)
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

    public function conversation($id){
        try{

            $blog = Blog::findOrFail(decrypt($id));
            return view('admin.users.blog', compact('blog'));
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong!');
        }
    }

}
