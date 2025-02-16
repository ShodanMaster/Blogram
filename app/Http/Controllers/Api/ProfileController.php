<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(){
        try{
            $blogs = Blog::where('user_id', Auth::user()->id)->latest()->paginate(10);
            $followingUsers = Auth::user()->following()->with('followedUser')->get();
            $followedUsers = Auth::user()->followers()->with('followerUser')->get();

            return response()->json([
               'status' => 200,
               'message' => 'User Found',
               'data' => [
                'blogs' => $blogs,
                'followingUsers' => $followingUsers,
                'followedUsers' => $followedUsers,
               ]
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong! '.$e->getMessage(),
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'gender' => 'required|in:Male,Female,Other',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {

            $profileData = [
                'gender' => $request->gender,
                'bio' => $request->bio,
            ];

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'profile_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('profile_images', $imageName, 'public');

                $this->deleteOldProfileImage();

                $profileData['profile_image'] = $imagePath;
            } elseif ($request->input('removeProfileImage')) {

                $this->deleteOldProfileImage();

                $profileData['profile_image'] = null;
            }

            $user = Auth::user();
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            return response()->json([
                'status' => 200,
                'message' => 'Profile Updated Successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function deleteOldProfileImage(){

        $userProfile = Auth::user()->profile;

        if ($userProfile && $userProfile->profile_image) {
            // Delete the old image from storage
            $oldImagePath = 'public/' . $userProfile->profile_image;

            if (Storage::exists($oldImagePath)) {
                Storage::delete($oldImagePath);
            }
        }
    }

    public function userProfile($id)
{
    try {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'message' => 'User Not Found',
            ], 404);
        }

        // If the authenticated user is viewing their own profile, return their details
        if (Auth::id() == $user->id) {
            return response()->json([
                'status' => 200,
                'message' => 'Authenticated User Profile',
                'data' => [
                    'user' => $user,
                    'blogs' => Blog::where('user_id', $user->id)->latest()->paginate(10),
                    'followingUsers' => $user->following()->with('followedUser')->get(),
                    'followedUsers' => $user->followers()->with('followerUser')->get(),
                ]
            ], 200);
        }

        // Fetch data for other users' profiles
        return response()->json([
            'status' => 200,
            'message' => 'User Found',
            'data' => [
                'user' => $user,
                'blogs' => Blog::where('user_id', $user->id)->latest()->paginate(10),
                'followingUsers' => $user->following()->with('followedUser')->get(),
                'followedUsers' => $user->followers()->with('followerUser')->get(),
            ]
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Something Went Wrong! ' . $e->getMessage(),
        ], 500);
    }
}


}
