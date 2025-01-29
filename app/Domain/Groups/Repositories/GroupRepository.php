<?php

namespace App\Domain\Groups\Repositories;

use App\Domain\Groups\Models\Group;

class GroupRepository
{
    public function getAllGroupDepartmentId($department_id)
    {
        $year = date('Y');
        $lastYear = substr($year, -2);
        return Group::query()
            ->whereRaw("RIGHT(name, 2) REGEXP '^[0-9]+$'") // Oxirgi 2 ta qiymat faqat raqam ekanligini tekshiramiz
            ->whereRaw("? - CAST(RIGHT(name, 2) AS UNSIGNED) <= 4", [$lastYear]) // Farq 4 bo‘lsa
            ->where('department_id',$department_id)
            ->get();
    }
}
