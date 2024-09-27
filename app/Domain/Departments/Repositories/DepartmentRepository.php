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
            ->where('name','not like','%(nofaol)%')
            ->where('h_structure_type',4140)    //FAKULTET
            ->get()
            ->sortBy('name');
    }
}
