<?php

namespace App\Domain\Syllabus\Repositories;

use App\Domain\Syllabus\Models\Syllabus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SyllabusRepository
{
    /**
     * @return Builder[]|Collection
     */
    public function getAll()
    {
        return Syllabus::query()
            ->orderBy('id')
            ->get();
    }
}
