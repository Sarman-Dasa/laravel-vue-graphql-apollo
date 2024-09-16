<?php
namespace App\GraphQL\Mutations;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeCommentMutation
{
    public function __invoke($root, array $args)
    {
        // $user = Auth::user();
        $user = User::findOrFail($args['user_id']);
        $comment = Comment::findOrFail($args['comment_id']);
       Log::info( $user);
        // Check if the user has already liked the comment
        if (!$user->likedComments()->where('comment_id', $args['comment_id'])->exists()) {
            $user->likedComments()->attach($comment->id);
        }
        else {
            $user->likedComments()->detach($comment->id);
        }

        return $comment;
    }
}
