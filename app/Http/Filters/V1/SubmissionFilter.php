<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class SubmissionFilter extends QueryFilter
{
    protected $sortable = [
        "submitted_at" => "self",
    ];

    public function submittedAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('submitted_at', $dates);
        }

        return $this->builder
            ->whereDate('submitted_at', $value);
    }
}
