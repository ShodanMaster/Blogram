<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    public function index(){
        return view('login.authenticate');
    }

    public function LoggingIn(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            $rememberMe = $request->has('rememberMe');

            if (auth()->guard('web')->attempt($credentials, $rememberMe)) {
                return redirect()->route('index')->with('success', 'Logged In');
            }elseif (auth()->guard('admin')->attempt($credentials, $rememberMe)) {
                return redirect()->route('admin.index')->with('success', 'Logged In');
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

    public function googlePage(){
        return Socialite::driver('google')->redirect();
    }

    public function googleCallBack(){
        try {

            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if($finduser)

            {

                Auth::login($finduser, true);

                return redirect()->intended('');

            }

            else

            {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => encrypt('123456dummy')
                ]);

                Auth::login($newUser, true);

                return redirect()->intended('');
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something Went Wrong! '.$e->getMessage());
        }
    }

    public function changePassword(){
        return view('login.changepassword');
    }

    public function passwordChange(Request $request){
        // dd($request->all());
        $request->validate([
            'currentpassword' => 'required|string|min:8',
            'password' => 'required|string|confirmed',
        ]);

        try{
            if (Hash::check($request->currentpassword, Auth::user()->password)) {
                Auth::user()->update([
                    'password' => bcrypt($request->password), // Fix typo: 'password' instead of 'passowrd'
                ]);
                return redirect()->back()->with('success','Password Changed Successfully');
            }else{
                return redirect()->back()->with('success','Current Password Does Not match');
            }
        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something went Wrong! '. $e->getMessage());
        }
    }

    public function loggingOut(){
        auth()->logout();
        return redirect()->route('login')->with('success', 'logged Out');
    }

}
