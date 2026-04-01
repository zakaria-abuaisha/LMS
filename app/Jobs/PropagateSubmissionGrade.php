<?php

namespace App\Jobs;

use App\Mail\SubmissionGraded;
use App\Models\Submission;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class PropagateSubmissionGrade implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Submission $submission)
    {
        //
    }

    /**
     * Send An Email to the student of this graded submission
     */
    public function handle(): void
    {
        Mail::to($this->submission->student->email)
            ->send(new SubmissionGraded($this->submission));

    }
}
