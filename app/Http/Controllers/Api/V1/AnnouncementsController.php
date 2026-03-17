<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\AnnouncementFilter;
use App\Http\Requests\Api\V1\Announcements\StoreAnnouncementRequest;
use App\Http\Requests\Api\V1\Announcements\UpdateAnnouncementRequest;
use App\Http\Resources\V1\AnnouncementResource;
use App\Jobs\PropagateAnnouncement;
use App\Models\Announcement;
use App\Models\Course;
use App\Policies\CoursePolicy;

class AnnouncementsController extends ApiController
{
    protected $policyClass = CoursePolicy::class;

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

    public function show(Announcement $announcement)
    {
        if ($this->isAble("IsForInstructor", $announcement->course) || $this->isAble("IsStudentEnrolled", $announcement->course))
        {
            $announcement->unsetRelation("course");
            $toBeIncluded = [
                "course"
            ];

            $announcement = $this->loadRelationships($announcement, $toBeIncluded);
            return new AnnouncementResource($announcement);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        if ($this->isAble("IsForInstructor", $announcement->course) || $this->isAble("IsStudentEnrolled", $announcement->course))
        {
            $announcement->update($request->mappedAttributes($request->all()));
            $announcement->refresh();

            return new AnnouncementResource($announcement);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(Announcement $announcement)
    {
        if ($this->isAble("IsForInstructor", $announcement->course))
        {
            $announcement->delete();

            return $this->ok('Announcement deleted successfully');
        }
        
        return $this->notAuthorized("NOT Authorized");
    }
}
