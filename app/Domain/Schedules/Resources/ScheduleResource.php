<?php

namespace App\Domain\Schedules\Resources;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Rooms\Models\Room;
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
//        dd($this);
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
            'camera' => CameraResource::collection(
                Camera::whereHas('rooms', function ($query) {
                    $query->where('rooms.code', $this->auditorium->code);
                })->get()
            )
//            'camera' => CameraResource::collection(Camera::whereHas('rooms', function ($query) {
//        $query->where('room_id', Room::query()->where('code',$this->auditorium->code)->first()->id);
//    })->get())
        ];
    }
}
