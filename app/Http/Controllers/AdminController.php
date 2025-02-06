<?php

namespace App\Http\Controllers;

use App\Models\User;
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

}
