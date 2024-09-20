<?php

namespace App\GraphQL\Mutations;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EditableComment {
    public function __invoke(Comment $comment, $args)
    {

        $createdDate = $comment->created_at;
        $fiveMinutes = now()->subMinutes(5);
        
        return $createdDate->gte($fiveMinutes);
    }
}