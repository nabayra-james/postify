<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserRegistration;
use Illuminate\Support\Facades\Storage;
use App\Models\UserPost;
use App\Models\UseFollow;

class FriendController extends Controller
{
    //get user details
    public function getUserDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();
    
            // gather user details into an array
            $userDetails = [
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'birthdate' => $user->birthdate,
            ];
    
            return $userDetails;
        }
    
        return null; // return null if the user is not authenticated
    }
    
    //view friends page
    public function friendsPage()
    {
        if (!auth()->check()) {
            return redirect()->route('login'); // redirect to login page if not authenticated
        }
    
        $userId = auth()->id();
        $userDetails = UserRegistration::find($userId);
    
        // fetch only mutual followers
        $mutualFollowers = UserRegistration::whereHas('followers', function ($query) use ($userId) {
            $query->where('follower_id', $userId);
        })->whereHas('following', function ($query) use ($userId) {
            $query->where('followed_id', $userId);
        })->where('user_id', '!=', $userId)->get();
    
        // create an associative array to check mutual follows
        $mutualFollows = $mutualFollowers->keyBy('user_id')->mapWithKeys(function ($item) use ($userId) {
            return [$item->user_id => $item->isFollowedBy($userId)];
        });
    
        return view('user.friends', [
            'userDetails' => $userDetails,
            'mutualFollowers' => $mutualFollowers,
            'userId' => $userId,
            'mutualFollows' => $mutualFollows,
        ]);
    }
    
    
    //follow user
    public function follow($userId)
    {
        $user = auth()->user();
        if ($user->follow($userId)) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400); // return 400 Bad Request if follow action fails
    }
    
    //unfollow user
    public function unfollow(Request $request, $userId)
    {
        $user = auth()->user();
    
        // remove the follow relationship
        $user->following()->detach($userId);
    
        return response()->json(['success' => true]);
    }
     
}
