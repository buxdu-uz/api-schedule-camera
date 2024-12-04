<?php

namespace App\Domain\GenerationSchedules\Actions;

use App\Domain\GenerationSchedules\DTO\StoreGenerationScheduleDTO;
use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\Syllabus\Models\Syllabus;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreGenerationScheduleAction
{
    /**
     * @param StoreGenerationScheduleDTO $dto
     * @return array
     * @throws Exception
     */
    public function execute(StoreGenerationScheduleDTO $dto): array
    {
        DB::beginTransaction();
        try {
            $syllabus = Syllabus::query()->latest()->first();
            $start_date = Carbon::parse($syllabus->start_date);
            $end_date = Carbon::parse($syllabus->end_date);
            $totalWeeks = $start_date->diffInWeeks($end_date);
            $datesForTargetDay = [];

            foreach ($dto->getData() as $data) {
                $date = Carbon::parse($data['date']);
                $subjectGroup = SubjectGroup::query()->find($data['subject_group_id']);

                if (!$subjectGroup) {
                    throw new Exception('Mavjud subject_group_id topilmadi.');
                }

                $totalLessons = round(($subjectGroup->lesson_hour / 2) / $totalWeeks);
                $weeklySchedule = [];

                for ($week = 0; $week <= $totalWeeks; $week++) {
                    $currentWeekDate = $date->copy()->addWeeks($week);

                    if (!$currentWeekDate->between($start_date, $end_date)) {
                        continue; // Faqat syllabus diapazonidagi sanalarni hisobga olamiz
                    }

                    $weekNumber = $currentWeekDate->weekOfYear;

                    if (!isset($weeklySchedule[$weekNumber])) {
                        $weeklySchedule[$weekNumber] = [];
                    }

                    // Haftadagi mavjud darslarni tekshirish
                    if (count($weeklySchedule[$weekNumber]) >= $totalLessons) {
                        continue; // Haftalik limitga erishilgan
                    }

                    $dayOfWeek = $currentWeekDate->dayOfWeek;

                    if (isset($weeklySchedule[$weekNumber][$dayOfWeek])) {
                        throw new Exception(
                            "Haftaning bir kunida faqat bitta dars qo'yilishi mumkin: " . $currentWeekDate->toDateString()
                        );
                    }

                    // Jadvalga dars qo'shish
                    $generationSchedule = new GenerationSchedule();
                    $generationSchedule->teacher_id = Auth::id();
                    $generationSchedule->subject_group_id = $data['subject_group_id'];
                    $generationSchedule->date = $currentWeekDate->toDateString();
                    $generationSchedule->pair = $data['pair'];
                    $generationSchedule->save();

                    // Haftalik jadvalni yangilash
                    $weeklySchedule[$weekNumber][$dayOfWeek] = $currentWeekDate->toDateString();

                    // Qo'shilgan sanani saqlash
                    $datesForTargetDay[$date->toDateString()][] = $currentWeekDate->toDateString();
                }

                // Fanni statusini yangilash
                $subjectGroup->update(['status' => true]);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $datesForTargetDay;

    }
}
