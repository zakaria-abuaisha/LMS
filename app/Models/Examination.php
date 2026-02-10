<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Examination extends Model
{
    use HasFactory;

    protected $fillable = [
        "type",
        "grade",
        "course_id",
        "student_id"
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
}
