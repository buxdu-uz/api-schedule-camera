<?php

namespace App\Domain\Schedules\Resources;

use App\Domain\Cameras\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'building' => $this->auditorium->building->name,
            'auditorium' => $this->auditorium->name,
            'teacher' => $this->employee->name,
            'group' => $this->group->name,
            'subject' => $this->subject->name,
            'lesson_pair' => [
                'start_time' => $this->lessonPair->start_time,
                'end_time' => $this->lessonPair->end_time,
            ],
            'camera' => Camera::whereHas('rooms', function ($query) {
                $query->where('room_id', $this->group->code);
            })->get()
        ];
    }
}
