<?php

namespace App\Domain\GenerationSchedules\Resources;

use App\Domain\Rooms\Models\Room;
use App\Domain\Rooms\Resources\RoomResource;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\SubjectGroups\Resources\SubjectGroupResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenerationScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'teacher' => $this->teacher->name,
            'subject' => $this->subjectGroup->subject->name,
            'building' => $this->room->building->name,
            'floor' => $this->room->floor->name ?? null,
            'room' => $this->room->name,
            'date' => $this->date,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'pair' => $this->pair,
            // Add more fields as needed
        ];
    }
}
