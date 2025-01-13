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
    public function getAllFakultet()
    {
        return Department::query()
            ->where('name','not like','%(nofaol)%')
            ->where('h_structure_type',4161)    //FAKULTET
            ->get()
            ->sortBy('name');
    }

    public function getAll($parent_id = null)
    {
        return Department::query()
        ->where('name', 'not like', '%(nofaol)%')
        ->when($parent_id !== null, function ($query) use ($parent_id) {
            $query->where('parent_id', $parent_id);
        }, function ($query) {
            // If parent_id is null, we only include where parent_id is null
            $query->where('parent_id', null);
        })
        ->get()
        ->sortBy('name');
    }
}
