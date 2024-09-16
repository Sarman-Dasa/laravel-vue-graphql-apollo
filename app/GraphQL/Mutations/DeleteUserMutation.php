<?php

namespace App\GraphQL\Mutations;

use App\Models\User;

class DeleteUserMutation
{
    /**
     * Handle the delete user mutation.
     *
     * @param  null  $rootValue
     * @param  array  $args
     * @return User|null
     */
    public function __invoke($rootValue, array $args)
    {
        $user = User::find($args['id']);

        if ($user) {
            $user->delete();
            return $user;
        }

        return null; // Or throw an exception if user not found
    }
}
