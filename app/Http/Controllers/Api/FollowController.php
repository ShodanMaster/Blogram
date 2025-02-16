<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggleFollow($userId){

        $user = Auth::user();
        $followedUser = User::findOrFail($userId);

        // Check if the user is already following
        if ($user->isFollowing($followedUser->id)) {
            // Unfollow the user
            $user->following()->where('followed_id', $followedUser->id)->delete();
            $message = 'You have unfollowed ' . $followedUser->name;
        } else {
            // Follow the user
            $user->following()->create(['followed_id' => $followedUser->id]);
            $message = 'You are now following ' . $followedUser->name;
        }

        // Return success response with updated follower count
        return response()->json([
            'status' => 200,
            'message' => $message,
            'followCount' => $followedUser->followers()->count(),
        ], 200);
    }

}
