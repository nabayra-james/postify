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

class UserController extends Controller
{
    // get the user details
    public function getUserDetails()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // gather the user details into an array
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

    // view the welcome page
    public function welcomePage()
    {
        return view("index"); // return the view for the welcome page
    }

    // view the login page
    public function loginPage()
    {
        return view("login"); // return the view for the login page
    }

    // view the account sign-up page 
    public function signUpPage()
    {
        return view('sign-up'); // return the view for the sign-up page
    }

    // post sign-up data
    public function storeUserDetails(Request $request)
    {
        // validate the incoming request data
        $validateData = $request->validate([
            'username' => 'required|unique:user_registration,username',
            'email' => 'required|email|unique:user_registration,email',
            'first-name' => 'required',
            'last-name' => 'required',
            'birthdate' => 'required|date',
            'password' => 'required|confirmed',
        ]);

        // create a new user registration instance
        $user = new UserRegistration();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->first_name = $request->input('first-name');
        $user->middle_name = $request->input('middle-name');
        $user->last_name = $request->input('last-name');
        $user->birthdate = $request->birthdate;
        $user->password = Hash::make($request->password); 

        // save the user details to the database
        $user->save();

        // redirect to the home page with a success message
        return redirect()->route('user.home')->with('success', 'Registration successful!');
    }

    // view the home page
    public function homePage()
    {
        // check if the user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login'); // redirect to login page if not authenticated
        }
        
        $userId = auth()->id();
        
        $userDetails = $this->getUserDetails();
        
        // get IDs of users that the current user follows
        $followedUsers = UserFollow::where('follower_id', $userId)
                                ->pluck('followed_id')
                                ->toArray();
        
        // retrieve the user's posts with privacy check, including user details, sorted by the latest
        $posts = UserPost::where(function($query) use ($userId, $followedUsers) {
            $query->where('privacy', 'Public')
                ->orWhere(function($query) use ($userId, $followedUsers) {
                    $query->where('privacy', 'Friends')
                        ->where(function($query) use ($userId, $followedUsers) {
                            // user can see posts if they follow the user or if the user follows them back
                            $query->whereIn('user_id', $followedUsers)
                                    ->orWhere('user_id', $userId);
                        });
                })
                ->orWhere(function($query) use ($userId) {
                    $query->where('privacy', 'Only Me')
                        ->where('user_id', $userId);
                });
        })
        ->with('user') // eager load the user relationship
        ->orderBy('created_at', 'desc')  // sort by creation date, latest first
        ->get();
        
        return view('user.home', compact('userDetails', 'posts', 'followedUsers'));
    }

    // check if the username and email already exist
    public function checkUsernameEmail(Request $request)
    {
        $exists = UserRegistration::where('username', $request->username)
            ->orWhere('email', $request->email)
            ->exists();

        $response = [
            'exists' => $exists,
            'username' => UserRegistration::where('username', $request->username)->exists(),
            'email' => UserRegistration::where('email', $request->email)->exists()
        ];

        return response()->json($response);
    }

    // handle the login post request
    public function loginPost(Request $request)
    {
        // validate the login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // authentication passed
            return redirect()->intended(route('user.home'));
        }
        
        // authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function follow(Request $request, $userId)
    {
        // check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['status' => 'unauthenticated'], 401); // 401 Unauthorized
        }

        $user = UserRegistration::find(Auth::id());

        if (!$user) {
            return response()->json(['status' => 'user_not_found'], 404); // 404 Not Found
        }

        try {
            if ($user->follow($userId)) {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'already_following']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500); // 500 Internal Server Error
        }
    }

    public function unfollow(Request $request, $userId)
    {
        // check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['status' => 'unauthenticated'], 401); // 401 Unauthorized
        }

        $user = UserRegistration::find(Auth::id());

        if (!$user) {
            return response()->json(['status' => 'user_not_found'], 404); // 404 Not Found
        }

        try {
            if ($user->unfollow($userId)) {
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'not_following']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500); // 500 Internal Server Error
        }
    }

    public function userLogout(Request $request)
    {
        Auth::logout(); // log out the user
        $request->session()->invalidate(); // invalidate the session
        $request->session()->regenerateToken(); // regenerate the CSRF token

        return redirect()->route('login'); // redirect to the login page
    }

    
    
}
