<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        "subject",
        "content",
        "course_id"
    ];

    public function course() : BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(AssignmentFile::class);
    }

    public function Submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
