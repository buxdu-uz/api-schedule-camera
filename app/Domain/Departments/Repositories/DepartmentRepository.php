<?php

namespace App\Domain\Departments\Repositories;

use App\Domain\Departments\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    /**
     * @return Department[]|Builder[]|Collection
     */
    public function getAll()
    {
        return Department::query()
            ->get()
            ->sortBy('name');
    }
}
