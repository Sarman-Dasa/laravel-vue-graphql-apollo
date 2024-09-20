<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\error;

class UpdateComment {
    public function __invoke($root, array $args)
    {
        $user = Auth::user();
        $comment = Comment::whereId($args['id'])->where('user_id',$user->id)->first();
        if($comment) {
            $comment->update(['comment' => $args['comment']]);
            return $comment;
        }
        else {
            return error("Does't have access to update this comment !");
        }
    }
}