<?php

namespace App\Jobs;

use App\Mail\AnnouncementPosted;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class PropagateAnnouncement implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Course $course,public Announcement $announcement)
    {
        //
    }

    /**
     * Send An Email to students once an announcement is created.
     */
    public function handle(): void
    {
        $enrollments = $this->course->enrollments;

        foreach ($enrollments as $enrollment) 
        {
            Mail::to($enrollment->student->email)
                ->send(new AnnouncementPosted($this->announcement));
        }
    }
}
