<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function loggingIn(Request $request){
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
                return response()->json([
                    'status' => 200,
                    'guard' => 'web',
                    'message' => 'Login successful',
                    'user' => new UserResource(auth()->guard('web')->user()), // Send user details as resource
                ], 200);
            }

            if (auth()->guard('admin')->attempt($credentials, $rememberMe)) {
                return response()->json([
                    'status' => 200,
                    'guard' => 'admin',
                    'message' => 'Login successful',
                    'user' => new AdminResource(auth()->guard('admin')->user()), // Send admin details as resource
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

    public function loggingOut(){

        try {

            auth()->logout();

            return response()->json([
                'status' => 200,
                'message' => 'Logged out successfully.',
            ], 200);

        } catch (Exception $e) {

            Log::error('Logout Failed: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

}
