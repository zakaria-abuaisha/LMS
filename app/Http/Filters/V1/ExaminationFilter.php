<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class ExaminationFilter extends QueryFilter
{
    protected $sortable = [
        "grade" => "self",
        "created_at" => "self",
    ];

    public function type($value)
    {
        return $this->builder
            ->where("type", '=', $value);
    }

    public function cratedAt($value) {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder
                ->whereBetween('created_at', $dates);
        }

        return $this->builder
            ->whereDate('created_at', $value);
    }
}
