<?php

namespace App\Domain\Users\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * @return Collection|array
     */
    public function getAllTeacher(): Collection|array
    {
        return User::role('teacher')
            ->orderBy('name')
            ->get();
    }
}
