<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Submission;
use App\Models\User;

class UserPolicy extends CoursePolicy
{
    public function DiscussionBelongToUser(User $user, Discussion $discussion)
    {
        return $user->id === $discussion->user_id;
    }

    public function CommentBelongToUser(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id;
    }

    public function SubmissionBelongsToStudent(User $user, Submission $submission)
    {
        return $user->id === $submission->student_id;
    }

    public function BeforeDueDate(User $user, Assignment $assignment)
    {
        return now()->lessThan($assignment->due_date);
    }
}
