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
use App\Policies\UserPolicy;

class AnnouncementsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Announcements
     * 
     * Get all Announcements of a particular course.
     * 
     * @group Manage Announcements
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=-created_at
     * @queryParam filter[title] Filter Announcements by the title. [quiz, mid, final].Example: *Adjustments on The Assignment!*
     * @queryParam filter[createdAt] Filter examinations by creation date date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT an enrolled student." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     */ 
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
     * Create an Announcement.
     * 
     * Create an Announcement by the instructor in a particular course.
     * Note: An Email will be sent for all enrolled students once an announcement is published.
     * @group Manage Announcements
     * @apiResource App\Http\Resources\V1\AnnouncementResource
     * @apiResourceModel App\Models\Announcement
     * @Response 200 scenario="When you are NOT the instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
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
     * Show a specific Announcement.
     * 
     * Display an individual announcement for.
     * * available relationships for this resource : 
     *      * course : The course that the examination belongs to.
     * @group Manage Announcements
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\ExaminationResource
     * @apiResourceModel App\Models\Examination
     * @Response 200 scenario="When you are NOT the instructor of the course, Or NOT even an enrolled student." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
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

    /**
     * Update an Announcement.
     * 
     * Update a specific Announcement by the instructor.
     * @group Manage Announcements
     * @apiResource App\Http\Resources\V1\AnnouncementResource
     * @apiResourceModel App\Models\Announcement
     * @Response 200 scenario="When you are NOT the instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        if ($this->isAble("IsForInstructor", $announcement->course))
        {
            $announcement->update($request->mappedAttributes($request->all()));
            $announcement->refresh();

            return new AnnouncementResource($announcement);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete an Announcement.
     * 
     * Delete a specific Announcement by the instructor.
     * @group Manage Announcements
     * @Response 200 scenario="When you are NOT the instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Announcement deleted successfully",
     *      "code": 200
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
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
