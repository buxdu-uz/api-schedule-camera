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
     * @param $role
     * @return Collection|array
     */
    public function getAllUser($role): Collection|array
    {
        return User::query()
            ->role($role)
            ->orderBy('name')
            ->get();
    }

    public function getAllDepartmentUser($department_id)
    {
        return User::query()
            ->whereHas('profile',function ($query) use ($department_id) {
                $query->where('department_id',$department_id);
            })
            ->orderBy('name')
            ->get();
    }
}
