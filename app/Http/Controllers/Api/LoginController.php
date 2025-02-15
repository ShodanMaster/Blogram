<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{

    public function register(Request $request){

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        try{

            User::Create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Registered Successfully!',
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong! '. $e->getMessage(),
            ]);

        }
    }

    public function loggingIn(Request $request){

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = $request->only('email', 'password');
            $rememberMe = $request->has('rememberMe');

            // Web guard authentication
            if (auth()->guard('webapi')->attempt($credentials, $rememberMe)) {
                $user = auth()->guard('webapi')->user();
                $token = auth()->guard('webapi')->attempt($credentials);

                return response()->json([
                    'status' => 200,
                    'guard' => 'web',
                    'message' => 'Login successful',
                    'token' => $token,
                ], 200);
            }

            // Admin API authentication
            if (auth()->guard('adminapi')->attempt($credentials, $rememberMe)) {
                $admin = auth()->guard('adminapi')->user();
                $token = auth()->guard('adminapi')->attempt($credentials);

                return response()->json([
                    'status' => 200,
                    'guard' => 'admin',
                    'message' => 'Login successful',
                    'token' => $token,
                ], 200);
            }

            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized: Incorrect email or password.',
            ], 401);

        } catch (Exception $e) {
            Log::error('Login Failed: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refreshToken(){

        $guards = ['webapi', 'adminapi'];

        try {
            foreach ($guards as $guard) {
                if (auth()->guard($guard)->check()) {
                    $newToken = auth()->guard($guard)->refresh();

                    return response()->json([
                        'status' => true,
                        'message' => 'New Access Token Generated',
                        'token' => $newToken,
                        'guard' => $guard,
                    ], 200);
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found or token cannot be refreshed.',
            ], 401);

        } catch (Exception $e) {
            Log::error('Token Refresh Failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function loggingOut(Request $request){

        try {

            $guards = ['webapi', 'adminapi'];

            foreach ($guards as $guard) {
                if (auth()->guard($guard)->check()) {
                    auth()->guard($guard)->logout();

                    return response()->json([
                        'status' => 200,
                        'message' => 'Logged out successfully.',
                        'guard' => $guard,
                    ], 200);
                }
            }

            return response()->json([
                'status' => 401,
                'message' => 'No authenticated user found.',
            ], 401);

        } catch (Exception $e) {
            Log::error('Logout Failed: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }


}
