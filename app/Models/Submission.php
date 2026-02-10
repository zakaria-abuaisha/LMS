<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        "grade",
        "submitted_at",
        "assignment_id",
        "student_id",
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }


    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }
}
