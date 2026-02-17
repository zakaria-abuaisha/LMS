<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        "course_name",
        "description",
        "course_code",
        "start_at",
        "end_at",
        "instructor_id",
        "assignment_percent",
        "quiz_percent",
        "mid_percent",
        "final_percent",
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lectures(): HasMany
    {
        return $this->hasMany(Lecture::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }   

    public function examinations(): HasMany
    {
        return $this->hasMany(Examination::class);
    } 

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    } 

    public function students(): BelongsToMany
    {
        return $this->BelongsToMany(
            User::class,
            Enrollment::class,
            "course_id",
            "student_id"
        );
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
}
