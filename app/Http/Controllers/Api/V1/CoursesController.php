<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\CourseFilter;
use App\Http\Requests\Api\V1\Courses\StoreCourseRequest;
use App\Http\Requests\Api\V1\Courses\UpdateCourseRequest;
use App\Http\Resources\V1\CourseResource;
use App\Models\Course;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CoursesController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get courses
     * 
     * Get all courses for a User(instructor)
     * 
     * @group Manage Courses
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=course_name,-start_at,end_at
     * @queryParam filter[courseName] Filter a course by courseName. Example: *Machine Learning*
     */ 
    public function index(CourseFilter $filter)
    {
        $userId = Auth::user()->id;

        return CourseResource::collection(
            Course::where("instructor_id", $userId)
            ->filter($filter)
            ->paginate()
        );
    }

    /**
     * Create a course
     * 
     * Create a new course by a user(instructor) 
     * 
     * @group Manage Courses
     */ 
    public function store(StoreCourseRequest $request)
    {
        $additionalAttrs = [
            "instructor_id" => $request->user()->id,
            "course_code" => Str::random(9)
        ];

        $attributes = array_merge($request->mappedAttributes(), $additionalAttrs);
        return new CourseResource(Course::create($attributes));
    }

    /**
     * Show a specific course
     * 
     * Display an individual course.
     * 
     * **Note:** The user must be either:
     * - The course instructor.
     * - Enrolled in the course.
     * 
     * @group Manage Courses
     * @queryParam include string data field(s) to include other relationshps. Seprate multiple fields with commas, Available relations: instructor, lectures, announcements, instructor, discussions, lectures. Example: include=instructor,lectures    
     */ 
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

    /**
     * Update a specific course
     * 
     * Update the specified course.
     * 
     * @group Manage Courses
     */ 
    public function update(UpdateCourseRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course))
        {
            $course->update($request->mappedAttributes());

            return new CourseResource($course);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a specific course
     * 
     * Delete the specified course.
     * 
     * @group Manage Courses
     */ 
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
