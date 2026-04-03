<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "statistics",
            "data" => [
                "average" => $this["average"],
                "mid" => $this["mid"],
                "final" => $this["final"],
            ]
        ];
    }
}
