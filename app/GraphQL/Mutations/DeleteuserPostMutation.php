<?php

namespace App\GraphQL\Mutations;

use App\Models\Post;

class DeleteuserPostMutation
{
    /**
     * Handle the delete user mutation.
     *
     * @param  null  $rootValue
     * @param  array  $args
     * @return Post|null
     */
    public function __invoke($rootValue, array $args)
    {
        $post = Post::find($args['id']);

        if ($post) {
            $post->delete();
            return $post;
        }

        return null; // Or throw an exception if user not found
    }
}
