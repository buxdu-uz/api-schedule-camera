<?php

namespace App\Domain\GenerationSchedules\Repositories;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class GenerationScheduleRepository
{
    /**
     * @return Builder[]|Collection
     */
    public function getOwnSchedule(): Collection|array
    {
        return GenerationSchedule::query()
            ->where('teacher_id',Auth::id())
            ->get()
            ->sortBy('subjectGroup.name');
    }
}
