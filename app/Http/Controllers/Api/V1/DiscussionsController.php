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
