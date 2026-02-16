<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\V1\CoursFilter;
use App\Http\Requests\Api\V1\Courses\StoreCourseRequest;
use App\Http\Resources\V1\CourseResource;
use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CoursesController extends ApiController
{
    protected $policyClass = CoursePolicy::class;

    public function index(CoursFilter $filter, Request $request)
    {
        $user = $request->user();

        return CourseResource::collection(
            Course::where("instructure_id", $user->id)
            ->filter($filter)
            ->paginate()
        );
    }

    public function store(StoreCourseRequest $request)
    {
        $additionalAttrs = [
            "instructure_id" => $request->user()->id,
            "course_code" => Str::random(9)
        ];

        $attributes = array_merge($request->mappedAttributes(), $additionalAttrs);
        return new CourseResource(Course::create($attributes));
    }

    public function show(Course $course)
    {
        if ($this->isAble("viewCourse", $course))
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

            return $course;
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
