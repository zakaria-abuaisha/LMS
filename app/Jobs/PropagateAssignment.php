<?php

namespace App\Jobs;

use App\Mail\AssignmentPosted;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class PropagateAssignment implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Course $course,public Assignment $assignment)
    {
        //
    }

    /**
     * Send An Email to students once an assignment is created.
     */
    public function handle(): void
    {
        $enrollments = $this->course->enrollments;

        foreach ($enrollments as $enrollment) 
        {
            Mail::to($enrollment->student->email)
                ->send(new AssignmentPosted($this->assignment));
        }
    }
}
