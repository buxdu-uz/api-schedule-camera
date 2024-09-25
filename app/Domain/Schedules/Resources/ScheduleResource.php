<?php

namespace App\Domain\Schedules\Resources;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Rooms\Models\Room;
use Carbon\Carbon;
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
        $weekStartTime = Carbon::createFromTimestamp($this->weekStartTime)->format('Y-m-d'); // Start of the week
        $weekEndTime = Carbon::createFromTimestamp($this->weekEndTime)->format('Y-m-d');
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
                'lesson_date' => Carbon::createFromTimestamp($this->lesson_date)->format('Y-m-d')
            ],
            'camera' => CameraResource::collection(
                Camera::whereHas('rooms', function ($query) {
                    $query->where('rooms.code', $this->auditorium->code);
                })->get()
            ),
            'weeks' => [
                'week_start_lesson' => $weekStartTime,
                'week_end_lesson' => $weekEndTime,
            ]
        ];
    }
}
