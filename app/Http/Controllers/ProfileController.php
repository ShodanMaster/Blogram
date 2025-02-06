<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
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

    public function userProfile($id){
        // dd($id);
        try{
            $user = User::find(decrypt($id));

            if($user){
                if(Auth::user() == $user){
                    return redirect()->route('profile.index');
                }else{
                    return view('profile.userprofile', compact('user'));
                }
            }
            else{
                return redirect()->back()->with('error', 'User Not Found');
            }

        }catch(Exception $e){
            return redirect()->back()->with('error', 'Something Went Wrong! '. $e->getMessage());
        }
    }

}
