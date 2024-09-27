<?php

namespace App\Domain\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'department' => $this->department->name ?? null,
            'employee_type' =>  $this->employeeType->name,
        ];
    }
}
