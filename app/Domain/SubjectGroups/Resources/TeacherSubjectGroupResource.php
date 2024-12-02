<?php

namespace App\Domain\SubjectGroups\Resources;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Groups\Resources\GroupResource;
use App\Domain\Rooms\Models\Room;
use App\Domain\Syllabus\Resources\SyllabusResource;
use App\Enums\FlowOrSplitGroup;
use App\Enums\LessonType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class TeacherSubjectGroupResource extends JsonResource
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
            'flow' => $this->flow instanceof FlowOrSplitGroup ? $this->flow->getTextValue() : FlowOrSplitGroup::from($this->flow)->getTextValue(),
            'split_group' => $this->split_group instanceof FlowOrSplitGroup ? $this->split_group->getTextValue() : FlowOrSplitGroup::from($this->split_group)->getTextValue(),
            'lesson_hour' => $this->lesson_hour,
            'education_year' => $this->educationYear->name,
            'semester' => new SyllabusResource($this->syllabi),
            'status' => $this->status,
            'department' => $this->teacher->profile->department->name ?? null,
            'groups' => GroupResource::collection($this->groups),
            'created-at' => $this->created_at,
        ];
    }
}
