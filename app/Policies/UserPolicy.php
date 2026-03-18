<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;

class UserPolicy extends CoursePolicy
{
    public function DiscussionBelongToUser(User $user, Discussion $discussion)
    {
        return $user->id === $discussion->user_id;
    }
}
