<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $primaryKey = 'comment_id';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id', 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(UserPost::class, 'post_id', 'user_post_id');
    }
}
