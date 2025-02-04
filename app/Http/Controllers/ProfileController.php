<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(){
        return view('profile.index');
    }

    public function updateProfile(Request $request){
        // dd($request->all());

        $validated = $request->validate([
            'gender' => 'required|in:Male,Female,Other',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        try{
            if($request->hasFile('profile_image')){
                $image = $request->file('profile_image');
                $imageName = 'profile_image_'.uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('profile_images', $imageName,'public');

                $userProfile = Auth::user()->profile;

                if ($userProfile && $userProfile->profile_image) {
                    // Get the full path of the old image
                    $oldImagePath = public_path('storage/' . $userProfile->profile_image);

                    // Check if the old image exists and delete it
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Delete the old image from storage
                    }
                }
                
                Auth::user()->profile()->updateOrCreate(
                    [],  // Empty conditions, so it will update the profile of the logged-in user
                    [
                        'gender' => $request->gender,
                        'bio' => $request->bio,
                        'profile_image' => $imagePath, // Save the image path
                    ]
                );
            }else{
                Auth::user()->profile()->updateOrCreate(
                    [],
                    [
                        'gender' => $request->gender,
                        'bio' => $request->bio,
                    ]
                );
            }

            return response()->json([
                'status' => 200,
                'message' => 'Profile Updated Successfully',
            ], 200);

        }catch(Exception $e){
            // dd($e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Something Went Wrong: '. $e->getMessage(),
            ], 500);
        }
    }

}
