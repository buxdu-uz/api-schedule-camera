<?php

namespace App\Domain\Buildings\Repositories;

use App\Domain\Buildings\Models\Building;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BuildingRepository
{
    /**
     * @return Collection|array
     */
    public function getAll(): Collection|array
    {
        return Building::query()
            ->get();
    }
}
