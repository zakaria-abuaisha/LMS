<?php

namespace App\Http\Filters\V1;

use App\Http\Filters\V1\QueryFilter;

class AnnouncementFilter extends QueryFilter
{
    protected $sortable = [
        "created_at" => "self",
    ];

    public function title($value) 
    {
        $likeStr = str_replace("*", "%", $value);

        return $this->builder
            ->where("title", "like", $likeStr);
    }

    public function createdAt($value)
    {
        $dates = explode("-", $value);

        if (count($dates) > 1) 
        {
            return $this->builder
                ->whereBetween("created_at", $dates);   
        }

        return $this->builder
            ->where("created_at","", $value);
    }
}
