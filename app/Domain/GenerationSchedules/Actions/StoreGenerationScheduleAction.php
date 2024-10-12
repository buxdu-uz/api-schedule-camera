<?php

namespace App\Domain\GenerationSchedules\Actions;

use App\Domain\GenerationSchedules\DTO\StoreGenerationScheduleDTO;
use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreGenerationScheduleAction
{
    /**
     * @param StoreGenerationScheduleDTO $dto
     * @return GenerationSchedule
     * @throws Exception
     */
    public function execute(StoreGenerationScheduleDTO $dto): GenerationSchedule
    {
        DB::beginTransaction();
        try {
            $generation_schedule = new GenerationSchedule();
            $generation_schedule->teacher_id = Auth::id();
            $generation_schedule->subject_group_id = $dto->getSubjectGroupId();
            $generation_schedule->room_id = $dto->getRoomId();
            $generation_schedule->date = $dto->getDate();
            $generation_schedule->start_at = $dto->getStartAt();
            $generation_schedule->end_at = $dto->getEndAt();
            $generation_schedule->pair = $dto->getPair();
            $generation_schedule->save();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $generation_schedule;
    }
}
