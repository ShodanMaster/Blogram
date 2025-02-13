<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow($userId)
    {
        $user = Auth::user();
        $followedUser = User::findOrFail($userId);

        // Check if already following
        if ($user->isFollowing($followedUser->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'You are already following this user.',
            ], 400);
        }

        // Follow the user
        $user->following()->create(['followed_id' => $followedUser->id]);

        // Return success response with updated follower count
        return response()->json([
            'status' => 200,
            'message' => 'You are now following ' . $followedUser->name,
            'followCount' => $followedUser->followers()->count(),
        ], 200);
    }

    public function unfollow($userId)
    {
        $user = Auth::user();
        $followedUser = User::findOrFail($userId);

        // Check if not following
        if (!$user->isFollowing($followedUser->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'You are not following this user.',
            ], 400);
        }

        // Unfollow the user
        $user->following()->where('followed_id', $followedUser->id)->delete();

        // Return success response with updated follower count
        return response()->json([
            'status' => 200,
            'message' => 'You have unfollowed ' . $followedUser->name,
            'followCount' => $followedUser->followers()->count(),
        ], 200);
    }

}
