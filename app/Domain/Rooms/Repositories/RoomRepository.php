<?php

namespace App\Domain\Rooms\Repositories;

use App\Domain\Rooms\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RoomRepository
{
    /**
     * @return Collection|array
     */
    public function getAll(): Collection|array
    {
        return Room::query()
            ->get();
    }
}
