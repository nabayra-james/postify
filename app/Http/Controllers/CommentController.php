<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\UserPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\UserRegistration;
use App\Models\UserFollow;
use App\Models\Like;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:user_post,user_post_id',
            'content' => 'required|string|max:255',
        ]);
    
        $comment = new Comment();
        $comment->user_id = Auth::id(); // Make sure this returns the correct user_id
        $comment->post_id = $request->post_id;
        $comment->content = $request->content;
        $comment->save();
    
        // Load the comment with the user relationship
        $comment->load('user');
    
        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }

    public function destroy(Comment $comment)
    {
        // Ensure that only the owner of the comment or an admin can delete it
        if (Auth::id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }

    public function toggleLike(Request $request)
    {
        // Validate the request
        $request->validate([
            'post_id' => 'required|exists:user_post,user_post_id',
        ]);

        $postId = $request->post_id;
        $userId = Auth::id();

        // Check if the user has already liked the post
        $like = Like::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($like) {
            // If the like exists, delete it (unlike)
            $like->delete();
            $liked = false;
        } else {
            // If the like does not exist, create it (like)
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId,
            ]);
            $liked = true;
        }

        // Get the updated like count
        $likeCount = Like::where('post_id', $postId)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likeCount' => $likeCount,
        ]);
    }
    
    
    

}
