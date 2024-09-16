<?php

namespace App\Domain\Cameras\Repositories;

use App\Domain\Cameras\Models\Camera;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CameraRepository
{
    /**
     * @param $filter
     * @param $paginate
     * @return LengthAwarePaginator
     */
    public function paginate($filter,$paginate): LengthAwarePaginator
    {
        return Camera::query()
            ->Filter($filter)
            ->orderByDesc('id')
            ->paginate($paginate);
    }
}
