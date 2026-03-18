<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class DiscussionFilter extends QueryFilter
{
    protected $sortable = [
        "content" => "self",
        "created_at" => "self",
    ];

    public function content($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder
            ->where("content", 'like', $likeStr);
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
}
