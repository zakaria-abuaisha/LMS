<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\AnnouncementFilter;
use App\Http\Requests\Api\V1\Announcements\StoreAnnouncementRequest;
use App\Http\Resources\V1\AnnouncementResource;
use App\Jobs\PropagateAnnouncement;
use App\Mail\AnnouncementPosted;
use App\Models\Announcement;
use App\Models\Course;
use App\Policies\CourseAnnouncementPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AnnouncementsController extends ApiController
{
    protected $policyClass = CourseAnnouncementPolicy::class;

    public function index(Course $course, AnnouncementFilter $filter)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course)) {
            return AnnouncementResource::collection(
                Announcement::where("course_id", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course)) 
        {
            $additionalAttributes = [
                "course_id" => $course->id,
            ];

            $announcement = Announcement::create($request->mappedAttributes($additionalAttributes));

            $annResource = new AnnouncementResource($announcement);

            PropagateAnnouncement::dispatch($course, $announcement);

            return $annResource;
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
