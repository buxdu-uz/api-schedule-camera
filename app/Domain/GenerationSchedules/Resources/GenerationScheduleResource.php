<?php

namespace App\Domain\GenerationSchedules\Resources;

use App\Domain\Groups\Resources\GroupResource;
use App\Domain\Rooms\Models\Room;
use App\Domain\Rooms\Resources\RoomResource;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\SubjectGroups\Resources\SubjectGroupResource;
use App\Enums\LessonType;
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
            'lesson' => LessonType::from($this->subjectGroup->lesson)->getTextValue(),
            'date' => $this->date,
            'pair' => $this->pair,
            'groups' => GroupResource::collection($this->subjectGroup->groups),
        ];
    }
}
