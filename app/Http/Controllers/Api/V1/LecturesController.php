<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Lectures\StoreLectureRequest;
use App\Http\Resources\V1\LectureResource;
use App\Models\Course;
use App\Models\Lecture;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Storage;

class LecturesController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Course Lectures
     * 
     * Get all Course Lectures of a particular course.
     * 
     * @group Manage Course Lectures
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
    public function index(Course $course)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course)) 
        {
            return LectureResource::collection(Lecture::where("course_id", $course->id)->paginate());
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Create a Lecture.
     * 
     * Upload a Lecture by the instructor.
     * 
     * @group Manage Course Lectures
     * @apiResource App\Http\Resources\V1\LectureResource
     * @apiResourceModel App\Models\Lecture
     * @Response 200 scenario="When you are NOT The instructor of the course." 
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
     * @Response 200 scenario="When lectureFile doesn't match any of the supported files extensions." 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The lecture file field must be a file of type: pdf, docx, zip.",
     *            "source": "lectureFile"
     *       }
     *    ]
     *}
     */
    public function store(StoreLectureRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course))
        {
            $fileName = $request->file("lectureFile")->getClientOriginalName();
            
            $additionalAttributes = [
                "course_id" => $course->id,
                "name" => $fileName
            ];

            return new LectureResource(Lecture::create($request->mappedAttributes($additionalAttributes)));
        }

        return $this->notAuthorized("NOT Authorized");
    }   

    /**
     * Show a specific Lecture.
     * 
     * Display an individual Lecture for (Resource not the file it self).
     * * available relationships for this resource : 
     *      * course : The course that the Lecture belongs to.
     * @group Manage Course Lectures
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\LectureResource
     * @apiResourceModel App\Models\Lecture
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
    public function show(Lecture $lecture)
    {
        if ($this->isAble("IsForInstructor", $lecture->course) || $this->isAble("IsStudentEnrolled", $lecture->course))
        {
            $lecture->unsetRelation("course");
            $toIncluded = [
                'course'
            ];

            $lecture = $this->loadRelationships($lecture, $toIncluded);

            return new LectureResource($lecture);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Download Lecture File.
     * 
     * Download the file attached to a specific Lecture.
     * This endpoint returns the actual file (PDF, DOCX, ZIP, etc.), not a JSON resource.
     * 
     * @group Manage Course Lectures
     * @responseHeader 200 Content-Type application/octet-stream
     * @responseHeader 200 Content-Disposition attachment; filename="lecture-file.ext"
     * @response 200 scenario="Successful download (binary file response)"
     * @Response 403 scenario="When you are NOT the instructor of the course, Or NOT an enrolled student."
     * {
     *      "errors": [{
     *          "status": 403,
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
    public function downloadLectureFile(Lecture $lecture)
    {
        if ($this->isAble("IsForInstructor", $lecture->course) || $this->isAble("IsStudentEnrolled", $lecture->course))
        {
            $lecture->unsetRelation("course");
            return Storage::disk('public')->download($lecture->path);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a Lecture.
     * 
     * Delete a specific Lecture from database and the storage.
     * @group Manage Course Lectures
     * @Response 200 scenario="When you're not the instructor."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Lecture deleted successfully",
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
    public function destroy(Lecture $lecture)
    {
        if ($this->isAble("IsForInstructor", $lecture->course))
        {
            if (Storage::disk("public")->exists($lecture->path))
                Storage::disk("public")->delete($lecture->path);

            $lecture->delete();

            return $this->ok('Lecture deleted successfully');
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
