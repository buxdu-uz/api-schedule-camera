<?php

namespace App\Domain\GenerationSchedules\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GenerationScheduleGroupedResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->collection->first()->date, // Assuming all schedules in this group have the same date
            'schedules' => GenerationScheduleResource::collection($this->collection), // Nested resources
        ];
    }
}
