<?php

namespace App\Domain\SubjectGroups\Resources;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Rooms\Models\Room;
use App\Enums\FlowOrSplitGroup;
use App\Enums\LessonType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SubjectGroupResource extends JsonResource
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
            'subject' => $this->subject->name,
            'lesson' => $this->getLessonType(),
            'flow' => $this->getFlow(),
            'split_group' => $this->getSplitGroup(),
            'lesson_hour' => $this->lesson_hour,
            'created-at' => $this->created_at
        ];
    }

    /**
     * @return string|null
     */
    public function getLessonType(): ?string
    {
        return $this->lesson ? LessonType::from($this->lesson)->getTextValue() : null;
    }

    /**
     * @return string|null
     */
    public function getFlow(): ?string
    {
        return $this->flow ? FlowOrSplitGroup::from($this->flow)->getTextValue() : null;
    }

    /**
     * @return string|null
     */
    public function getSplitGroup(): ?string
    {
        return $this->split_group ? FlowOrSplitGroup::from($this->split_group)->getTextValue() : null;
    }
}
