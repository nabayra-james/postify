<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\UserRegistration;
use Illuminate\Support\Facades\Storage;
use App\Models\UserPost;

class PostController extends Controller
{
    // store a new post
    public function storePost(Request $request)
    {
        $request->validate([
            'caption' => 'required|string|max:255',
            'image' => 'nullable|image|max:50000', // 50MB max
            'privacy' => 'required|in:Public,Friends,Only Me',
        ]);

        $post = new UserPost();
        $post->user_id = Auth::id(); // set the user ID for the post
        $post->caption = $request->caption;
        $post->privacy = $request->privacy;

        // handle image upload if provided
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image = $imagePath;
        }

        $post->save(); // save the new post

        return redirect()->route('user.home')->with('success', 'Post created successfully.');
    }

    // show the edit form for a post
    public function edit($id)
    {
        // store the current URL before showing the edit form
        session()->put('intended_url', url()->previous());

        $post = UserPost::with('user')->findOrFail($id);
        return view('user.home', compact('post'));
    }

    // update an existing post
    public function update(Request $request, UserPost $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized access.'], 403);
        }

        // validate request data
        $validator = Validator::make($request->all(), [
            'caption' => 'required|string|max:255',
            'privacy' => 'required|in:Public,Friends,Only Me',
            'image' => 'nullable|image|max:20480', 
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        // update post caption and privacy
        $post->caption = $request->caption;
        $post->privacy = $request->privacy;

        // handle image upload
        try {
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // delete old image if it exists
                if ($post->image) {
                    Storage::delete('public/' . $post->image);
                }
                // store new image
                $imagePath = $request->file('image')->store('posts', 'public');
                $post->image = $imagePath;
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Image upload failed: ' . $e->getMessage()], 500);
        }

        // save the updated post
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully.',
            'post' => $post
        ]);
    }

    // delete a post
    public function destroy($user_post_id)
    {
        $post = UserPost::find($user_post_id);

        if ($post) {
            $post->delete();
            return response()->json(['success' => true], 200);
        }

        return response()->json(['success' => false, 'message' => 'Post not found'], 404);
    }
}
