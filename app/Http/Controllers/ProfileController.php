<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; 
use App\Models\UserRegistration;
use App\Models\UserPost;
use App\Models\UserFollow;

class ProfileController extends Controller
{
   // get user details
    public function getUserDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // access user details
            return [
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'birthdate' => $user->birthdate,
            ];
        }

        return null;
    }

    // view profile page
    public function profilePage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

        // get the user's details
        $userDetails = UserRegistration::find($userId);

        // retrieve posts with comments and user details
        $posts = UserPost::with('comments.user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // generate the profile picture URL
        $profilePictureUrl = auth()->user()->profile_picture
            ? asset('storage/' . auth()->user()->profile_picture)
            : asset('assets-user/img/none-profile.jpg');

        // pass the data to the view
        return view('user.profile', [
            'userDetails' => $userDetails,
            'posts' => $posts,
            'postCount' => $userDetails->postCount(),
            'followersCount' => $userDetails->followersCount(),
            'followingCount' => $userDetails->followingCount(),
            'profilePictureUrl' => $profilePictureUrl,
        ]);
    }

    // view edit profile page
    public function editProfilePage()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $userId = auth()->id();
        $userDetails = UserRegistration::find($userId);
        
        // pass user details to the view
        return view('user.edit-profile', [
            'userDetails' => $userDetails,
        ]);
    }

    // update profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // validate the request
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:user_registration,username,' . $user->user_id . ',user_id',
            'email' => 'required|string|email|max:255|unique:user_registration,email,' . $user->user_id . ',user_id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:50000',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:50000', 
        ]);

        // update user details
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'];
        $user->last_name = $validated['last_name'];
        $user->birthdate = $validated['birthdate'];

        // handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // check if file upload is valid
            if ($file->isValid()) {
                $path = $file->store('profile_pictures', 'public');

                // log the path to see if it is being set
                \Log::info('Profile picture path: ' . $path);

                // manually set the profile_picture attribute
                $user->profile_picture = $path;
            }
        } else {
            \Log::info('No profile picture uploaded');
        }

        // handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            $coverFile = $request->file('cover_photo');

            // check if file upload is valid
            if ($coverFile->isValid()) {
                $coverPath = $coverFile->store('cover_photos', 'public');

                // log the path to see if it is being set
                \Log::info('Cover photo path: ' . $coverPath);

                // manually set the cover_photo attribute
                $user->cover_photo = $coverPath;
            }
        } else {
            \Log::info('No cover photo uploaded');
        }

        // attempt to manually update the database directly
        $affected = \DB::table('user_registration')
            ->where('user_id', $user->user_id)
            ->update([
                'profile_picture' => $user->profile_picture,
                'cover_photo' => $user->cover_photo,
            ]);

        \Log::info('Affected rows: ' . $affected);

        // save updated user profile
        $user->save();

        return redirect()->route('user.edit-profile')->with('success', 'Profile updated successfully!');
    }
}