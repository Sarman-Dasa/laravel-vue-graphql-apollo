<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class CommentLikeStatus
{
    public function __invoke(Comment $comment, $args)
    {
        $user = Auth::user();
        Log::info("Session Data: " . json_encode($user));

        // $user = User::findOrFail($args['user_id']);
        if (!$user) {
            return false; // If no user is authenticated, return false
        }

        return $user->likedComments()->where('comment_id', $comment->id)->exists();
    }
}
