<?php

namespace App\Domain\Syllabus\Repositories;

use App\Domain\Syllabus\Models\Syllabus;
use Illuminate\Database\Eloquent\Builder;

class SyllabusRepository
{
    /**
     * @return Builder
     */
    public function latest()
    {
        return Syllabus::query()
            ->latest()
            ->first();
    }
}
