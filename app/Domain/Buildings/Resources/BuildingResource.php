<?php

namespace App\Domain\Buildings\Resources;

use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Rooms\Resources\RoomResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
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
            'rooms' => RoomResource::collection($this->rooms),
        ];
    }
}
