<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use Error;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

class UpdateComment
{
    public function __invoke($root, array $args)
    {
        $user = Auth::id();
        $comment = Comment::where('id', $args['id'])->where('user_id', $user)->first();

        if (!$comment) {
            throw new Error("You don't have access to update this comment!");
        }

        $fiveMinutesAgo = now()->subMinutes(5);

        if ($comment->created_at->gte($fiveMinutesAgo)) {
            $comment->update(['comment' => $args['comment']]);
            return $comment;
        }

        throw new Error("The comment is no longer updateable after 5 minutes!");
    }
}
