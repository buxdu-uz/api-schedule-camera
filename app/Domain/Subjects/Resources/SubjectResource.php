<?php

namespace App\Domain\Subjects\Resources;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Rooms\Models\Room;
use App\Enums\FlowOrSplitGroup;
use App\Enums\LessonType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SubjectResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'education_type' => $this->educationType->name,
            'subject_block' => $this->subjectBlock->name ??null,
        ];
    }
}
