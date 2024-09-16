<?php

namespace App\Domain\Users\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * @param $filter
     * @param $paginate
     * @return LengthAwarePaginator
     */
    public function paginate($filter,$paginate): LengthAwarePaginator
    {
        return User::query()
            ->Filter($filter)
            ->orderByDesc('id')
            ->paginate($paginate);
    }
}
