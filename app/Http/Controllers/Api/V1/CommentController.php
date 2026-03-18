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
