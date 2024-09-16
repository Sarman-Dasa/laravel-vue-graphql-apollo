<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // protected $fillable = ['comment', 'post_id', 'user_id', 'likes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Relationship with Users who liked the comment
    public function likes()
    {
        return $this->belongsToMany(User::class, 'comment_likes')
                    ->withTimestamps();
    }
}
