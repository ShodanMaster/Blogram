<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index(){
        return view('login.authenticate');
    }

    public function LoggingIn(Request $request){

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (auth()->attempt($credentials)) {
                return redirect()->route('index')->with('success', 'Logged In');
            } else {
                return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
            }

        } catch (Exception $e) {
            Log::error('Login Failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function registerUser(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8'
        ]);

        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            if($user && auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Registered Successfully',
                    'url' => route('index'),
                ]);
            }

            return response()->json([
                'status' => 400,
                'message' => 'Authentication Failed.',
            ], 400);


        }catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong. Please try again.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(){
        return view('login.changepassword');
    }

    public function passwordChange(Request $request){
        // dd($request->all());
        return redirect()->back()->with('success', 'Hai');

    }


    public function loggingOut(){
        auth()->logout();
        return redirect()->route('login')->with('success', 'logged Out');
    }

}
