<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Comments\StoreCommentRequest;
use App\Http\Requests\Api\V1\Comments\UpdateCommentRequest;
use App\Http\Resources\V1\CommentResource;
use App\Models\Comment;
use App\Models\Discussion;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class CommentController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Discussion's Comments
     * 
     * Get all Comments of a particular Discussion.
     * 
     * @group Manage Discussions Comments
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
    public function index(Discussion $discussion)
    {
        if ($this->isAble("IsForInstructor", $discussion->course) || $this->isAble("IsStudentEnrolled", $discussion->course))
        {
            return CommentResource::collection(
                Comment::where("discussion_id", $discussion->id)->paginate()
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Show a specific Comment.
     * 
     * Display an individual Comment for a particular Discussion.
     * * available relationships for this resource : 
     *      * discussion : The discussion that the comment belongs to.
     *      * user : The creator of that comment.
     * @group Manage Discussions Comments
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\CommentResource
     * @apiResourceModel App\Models\Comment
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
    public function show(Comment $comment)
    {
        if ($this->isAble("IsForInstructor", $comment->discussion->course) || $this->isAble("IsStudentEnrolled", $comment->discussion->course))
        {
            $comment->unsetRelation("discussion");

            $toBeIncluded = [
                "user",
                "discussion"
            ];

            $comment = $this->loadRelationships($comment, $toBeIncluded);
            return new CommentResource($comment);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Create a Comment.
     * 
     * Create a Comment by a user.
     * 
     * @group Manage Discussions Comments
     * @apiResource App\Http\Resources\V1\CommentResource
     * @apiResourceModel App\Models\Comment
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
     * @Response 200 scenario="When The content field exceeds 2500 characters" 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The data.attributes.content field must not be greater than 2500 characters.",
     *            "source": "data.attributes.content"
     *       }
     *    ]
     *}
     */
    public function store(StoreCommentRequest $request, Discussion $discussion)
    {
        if ($this->isAble("IsForInstructor", $discussion->course) || $this->isAble("IsStudentEnrolled", $discussion->course))
        {
            $additionalAttributes = [
                "discussion_id" => $discussion->id,
                "user_id" => Auth::user()->id,
            ];

            return new CommentResource(
                Comment::create($request->mappedAttributes($additionalAttributes))
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Update a Comment.
     * 
     * Update a specific Comment.
     * @group Manage Discussions Comments
     * @apiResource App\Http\Resources\V1\CommentResource
     * @apiResourceModel App\Models\Comment
     * @Response 200 scenario="When you're not enrolled, or you're not the owner of the comment." 
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
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($this->isAble("IsStudentEnrolled", $comment->discussion->course) && $this->isAble("CommentBelongToUser", $comment))
        {
            $comment->unsetRelation("discussion");
            $comment->update($request->mappedAttributes());

            return new CommentResource($comment);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a Comment.
     * 
     * Delete a specific Comment.
     * @group Manage Discussions Comments
     * @Response 200 scenario="When you're not enrolled, or you're not the owner of the comment."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Comment deleted successfully",
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
    public function destroy(Comment $comment)
    {
        if ($this->isAble("IsForInstructor", $comment->discussion->course) || $this->isAble("CommentBelongToUser", $comment))
        {
            $comment->delete();

            return $this->ok('Comment deleted successfully');
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
