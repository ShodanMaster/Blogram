<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(){
        $blogs = Blog::where('user_id', Auth::user()->id)->latest()->paginate(10);
        // dd($blogs->toArray());
        return view('profile.index', compact('blogs'));

    }

    public function updateProfile(Request $request){

        $validated = $request->validate([
            'gender' => 'required|in:Male,Female,Other',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle profile image upload if present
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = 'profile_image_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('profile_images', $imageName, 'public');

                // Delete old profile image if exists
                $this->deleteOldProfileImage();

                // Update the user's profile with new image
                if(Auth::user()->profile()){

                    Auth::user()->profile()->update([
                        'gender' => $request->gender,
                        'bio' => $request->bio,
                        'profile_image' => $imagePath,
                    ]);
                }else{
                    Auth::user()->profile()->create([
                        'gender' => $request->gender,
                        'bio' => $request->bio,
                        'profile_image' => $imagePath,
                    ]);
                }

            } else {
                // Handle profile image removal if requested
                if ($request->input('removeProfileImage')) {
                    $this->deleteOldProfileImage();

                    // Update profile without an image
                    Auth::user()->profile()->update([
                        'profile_image' => null,
                    ]);
                }

                // Update profile with gender and bio
                Auth::user()->profile()->updateOrCreate([
                    'gender' => $request->gender,
                    'bio' => $request->bio,
                ]);
            }

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


}
