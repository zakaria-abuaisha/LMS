<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\CourseFilter;
use App\Http\Requests\Api\V1\Courses\StoreCourseRequest;
use App\Http\Requests\Api\V1\Courses\UpdateCourseRequest;
use App\Http\Resources\V1\CourseResource;
use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CoursesController extends ApiController
{
    protected $policyClass = CoursePolicy::class;

    public function index(CourseFilter $filter)
    {
        $userId = Auth::user()->id;

        return CourseResource::collection(
            Course::where("instructor_id", $userId)
            ->filter($filter)
            ->paginate()
        );
    }

    public function store(StoreCourseRequest $request)
    {
        $additionalAttrs = [
            "instructor_id" => $request->user()->id,
            "course_code" => Str::random(9)
        ];

        $attributes = array_merge($request->mappedAttributes(), $additionalAttrs);
        return new CourseResource(Course::create($attributes));
    }

    public function show(Course $course)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course))
        {
            $toBeIncluded = [
                'instructor',
                'lectures',
                'announcements',
                'discussions',
                'enrollments',
                'examinations',
                'assignments',
                'students'
            ];
            
            $course = $this->loadRelationships($course, $toBeIncluded);

            return new CourseResource($course);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course))
        {
            $course->update($request->mappedAttributes());

            return new CourseResource($course);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(Course $course)
    {
        if($this->isAble("IsForInstructor", $course))
        {
            $course->delete();

            return $this->ok('Course deleted successfully');
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
