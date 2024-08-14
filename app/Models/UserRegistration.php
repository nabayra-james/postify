<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\UserFollow;


class UserRegistration extends Authenticatable
{
    use HasFactory;

    protected $table = 'user_registration';

    protected $primaryKey = 'user_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;
    
    protected $fillable = [
        'username',
        'email',
        'first_name',
        'middle_name',
        'last_name',
        'birthdate',
        'password',
        'profile_picture', 
        'cover_photo',
    ];
    
    protected $hidden = [
        'password',
    ];
    
    protected $casts = [
        'birthdate' => 'date',
    ];
    
    public function posts()
    {
        return $this->hasMany(UserPost::class, 'user_id', 'user_id');
    }
    
    public function follow($followedUserId)
    {
        $followedUser = UserRegistration::find($followedUserId);
    
        if (!$followedUser) {
            return false;
        }
    
        $existingFollow = UserFollow::where('follower_id', $this->user_id)
                                    ->where('followed_id', $followedUserId)
                                    ->exists();
    
        if ($existingFollow) {
            return true;
        }
    
        try {
            UserFollow::create([
                'follower_id' => $this->user_id,
                'followed_id' => $followedUserId,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error following user: ' . $e->getMessage());
            return false;
        }
    }
    
    public function unfollow($followedUserId)
    {
        $followedUser = UserRegistration::find($followedUserId);
    
        if (!$followedUser) {
            return false;
        }
    
        $existingFollow = UserFollow::where('follower_id', $this->user_id)
                                    ->where('followed_id', $followedUserId)
                                    ->first();
    
        if (!$existingFollow) {
            return false;
        }
    
        try {
            $existingFollow->delete();
            return true;
        } catch (\Exception $e) {
            \Log::error('Error unfollowing user: ' . $e->getMessage());
            return false;
        }
    }
    
    public function postCount()
    {
        return $this->posts()->count();
    }
    
    public function followersCount()
    {
        return UserFollow::where('followed_id', $this->user_id)->count();
    }
    
    public function followingCount()
    {
        return UserFollow::where('follower_id', $this->user_id)->count();
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }
    
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', 'user_id');
    }
    
    public function isFollowing($userId)
    {
        return UserFollow::where('follower_id', $this->user_id)
                        ->where('followed_id', $userId)
                        ->exists();
    }
    
    public function isFollowedBy($userId)
    {
        return UserFollow::where('follower_id', $userId)
                        ->where('followed_id', $this->user_id)
                        ->exists();
    }
    
    public function followers()
    {
        return $this->belongsToMany(UserRegistration::class, 'user_follows', 'followed_id', 'follower_id');
    }
    
    public function following()
    {
        return $this->belongsToMany(UserRegistration::class, 'user_follows', 'follower_id', 'followed_id');
    }

}

