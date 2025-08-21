<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function manageTodoList(User $user, Todo $todo)
    {
        return $todo->members()->where('user_id', $user->id)->exists();
    }
}
