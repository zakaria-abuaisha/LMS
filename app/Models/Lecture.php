<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        "path",
        "course_id"
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
}
