<?php

namespace App\GraphQL\Mutations;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostData
{
    public function resolve($root, array $args)
    {
        // Log::info('PostData resolver called with arguments: ', $args);

        $response =  Post::with(['comments.children' => function($q) {
            $q->whereNull('parent_id');
        }])->findOrFail($args['id']);
        Log::info("data",["data" => $response]);
        return $response;
    }
}

