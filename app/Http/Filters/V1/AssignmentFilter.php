<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class AssignmentFilter extends QueryFilter
{
    protected $sortable = [
        "subject" => "self",
        "created_at" => "self",
        "due_date" => "self",
    ];

    public function subject($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder
            ->where("subject", 'like', $likeStr);
    }

    public function createdAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('created_at', $dates);
        }

        return $this->builder
            ->whereDate('created_at', $value);
    }

    public function dueDate($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('due_date', $dates);
        }

        return $this->builder
            ->whereDate('due_date', $value);
    }
}
