<?php

namespace App\Domain\SubjectGroups\Repositories;

use App\Domain\SubjectGroups\Models\SubjectGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubjectGroupRepository
{
    /**
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return SubjectGroup::query()
            ->orderByDesc('id')
            ->paginate();
    }
}
