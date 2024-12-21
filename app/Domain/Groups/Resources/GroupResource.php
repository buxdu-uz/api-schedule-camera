<?php

namespace App\Domain\Groups\Resources;

use App\Domain\Users\Resources\UserResource;
use App\Domain\Users\Resources\UserRoleResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        dd($this->pivot->teacher_id);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'department' => $this->department->name,
            'teacher' => $this->pivot->teacher_id ? new UserRoleResource(User::query()->find($this->pivot->teacher_id)) : null
//            'speciality' => $this->speciality->name,
        ];
    }
}
