<?php

namespace App\Domain\Groups\Repositories;

use App\Domain\Groups\Models\Group;

class GroupRepository
{
    public function getAllGroupDepartmentId($department_id)
    {
        return Group::query()
            ->where('department_id',$department_id)
            ->get();
    }
}
