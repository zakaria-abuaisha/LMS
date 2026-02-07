<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses() : HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments() : HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function examinations() : HasMany
    {
        return $this->hasMany(Examination::class);
    }

    public function submissions() : HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function discussions() : HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function enrolledCourses() : BelongsToMany
    {
        return $this->BelongsToMany(
            Course::class, 
            Enrollment::class,
            "student_id",
            "course_id"
        );
    }
}
