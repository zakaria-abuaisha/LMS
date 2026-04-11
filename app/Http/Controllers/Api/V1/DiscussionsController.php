<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\DiscussionFilter;
use App\Http\Requests\Api\V1\Discussions\StoreDiscussionsRequest;
use App\Http\Requests\Api\V1\Discussions\UpdateDiscussionsRequest;
use App\Http\Resources\V1\DiscussionResource;
use App\Models\Course;
use App\Models\Discussion;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class DiscussionsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Discussions
     * 
     * Get all Discussions of a particular course.
     * 
     * @group Manage Discussions
     * @apiResourceCollection App\Http\Resources\V1\DiscussionResource
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=-created_at
     * @queryParam filter[content] Filter discussions by the title. Example: *What do you think of Linear regression ?*
     * @queryParam filter[createdAt] Filter discussions by creation date date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT an enrolled student." 
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
    public function index(Course $course, DiscussionFilter $filter)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course))
        {
            return DiscussionResource::collection(
                Discussion::where("course_id", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Create a Discussion.
     * 
     * Create an Discussion by the instructor or any student in a particular course.
     * 
     * @group Manage Discussions
     * @apiResource App\Http\Resources\V1\DiscussionResource
     * @apiResourceModel App\Models\Discussion
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT an enrolled student." 
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
     * @Response 200 scenario="When The content field exceeds 5000 characters" 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The data.attributes.content field must not be greater than 5000 characters.",
     *            "source": "data.attributes.content"
     *       }
     *    ]
     *}
     */
    public function store(StoreDiscussionsRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course))
        {
        
            $additionalAttributes = [
                "course_id" => $course->id,
                "user_id" => Auth::user()->id
            ];
            
            return new DiscussionResource(
                Discussion::create($request->mappedAttributes($additionalAttributes))
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Show a specific Discussion.
     * 
     * Display an individual Discussion for a particular course.
     * * available relationships for this resource : 
     *      * course : The course that the discussion belongs to.
     *      * user : The creator of that discussion.
     * @group Manage Discussions
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\DiscussionResource
     * @apiResourceModel App\Models\Discussion
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
    public function show(Discussion $discussion)
    {
        if ($this->isAble("IsForInstructor", $discussion->course) || $this->isAble("IsStudentEnrolled", $discussion->course))
        {
            $discussion->unsetRelation("course");

            $toBeIncluded = [
                "course",
                "user"
            ];
            $discussion = $this->loadRelationships($discussion, $toBeIncluded);

            return new DiscussionResource($discussion);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Update a Discussion.
     * 
     * Update a specific Discussion..
     * @group Manage Discussions
     * @apiResource App\Http\Resources\V1\DiscussionResource
     * @apiResourceModel App\Models\Discussion
     * @Response 200 scenario="When you're not enrolled, or you're not the owner of the discussion." 
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
     * @Response 200 cenario="When The content field exceeds 5000 characters" 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The data.attributes.content field must not be greater than 5000 characters.",
     *            "source": "data.attributes.content"
     *       }
     *    ]
     *}
     */
    public function update(UpdateDiscussionsRequest $request, Discussion $discussion)
    {
        if ($this->isAble("IsStudentEnrolled", $discussion->course) && $this->isAble("DiscussionBelongToUser", $discussion->course))
        {
            $discussion->unsetRelation("course");
            $discussion->update($request->mappedAttributes());

            return new DiscussionResource($discussion);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a Discussion.
     * 
     * Delete a specific Discussion.
     * @group Manage Discussions
     * @Response 200 scenario="When you're not the instructor, or you're not the owner of the discussion."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Discussion deleted successfully",
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
    public function destroy(Discussion $discussion)
    {
        if($this->isAble("IsForInstructor", $discussion->course) || $this->isAble("DiscussionBelongToUser", $discussion))
        {
            $discussion->delete();

            return $this->ok('Discussion deleted successfully');
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
