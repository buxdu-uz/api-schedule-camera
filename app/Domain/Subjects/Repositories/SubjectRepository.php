<?php

namespace App\Domain\Subjects\Repositories;

use App\Domain\Subjects\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository
{
    /**
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return Subject::query()
            ->orderByDesc('id')
            ->paginate();
    }

    /**
     * @return Collection|Builder[]
     */
    public function getAll(): array|Collection
    {
        return Subject::query()
            ->orderByDesc('id')
            ->get()
            ->sortBy('name');
    }
}
