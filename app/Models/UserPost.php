<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    use HasFactory;

    protected $table = 'user_post';

    protected $primaryKey = 'user_post_id';

    protected $fillable = [
        'user_id',
        'caption',
        'image',
        'privacy', 
    ];
    
    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'user_post_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id');
    }
}
