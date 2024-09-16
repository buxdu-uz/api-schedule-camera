<?php

namespace App\Domain\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'hemis_id' => $this->employee_id,
            'avatar' => $this->avatar,
            'login' => $this->login,
            'profile' => new UserProfileResource($this->profile)
        ];
    }
}
