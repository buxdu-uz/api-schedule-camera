<?php

namespace App\Domain\Cameras\Repositories;

use App\Domain\Cameras\Models\Camera;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CameraRepository
{
    /**
     * @param $paginate
     * @return LengthAwarePaginator
     */
    public function paginate($paginate): LengthAwarePaginator
    {
        return Camera::query()
            ->orderByDesc('id')
            ->paginate($paginate);
    }
}
