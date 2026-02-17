<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class CoursFilter extends QueryFilter
{
    protected $sortable = [
        "course_name" => "self",
        "start_at" => "self",
        "end_at" => "self",
    ];

    public function courseName($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder
            ->where("course_name", 'like', $likeStr);
    }

    public function startAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('start_at', $dates);
        }

        return $this->builder
            ->whereDate('start_at', $value);
    }

    public function endAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('end_at', $dates);
        }

        return $this->builder
            ->whereDate('end_at', $value);
    }
}
