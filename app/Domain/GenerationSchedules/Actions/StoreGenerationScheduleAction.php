<?php

namespace App\Domain\GenerationSchedules\Actions;

use App\Domain\GenerationSchedules\DTO\StoreGenerationScheduleDTO;
use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\Syllabus\Models\Syllabus;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            foreach ($dto->getData() as $data) {
                $syllabus = Syllabus::query()->find($data['syllabi_id']);
                $start_date = Carbon::parse($syllabus->start_date);
                $end_date = Carbon::parse($syllabus->end_date);
                $totalWeeks = $start_date->diffInWeeks($end_date);
                $datesForTargetDay = [];
                $date = Carbon::parse($data['date']);
                $subjectGroup = SubjectGroup::query()->find($data['subject_group_id']);

                if (!$subjectGroup) {
                    throw new Exception('Mavjud subject_group_id topilmadi.');
                }

                $subjectGroupTeacher = $subjectGroup->whereHas('groups', function ($query) {
                    $query->whereNotNull('teacher_id');
                })->first();

                if ($totalWeeks === 0) {
                    // Syllabusda faqat bitta hafta bor, requestdan kelayotgan datega dars qo'shish
                    if ($date->isPast()) {
                        throw new Exception("Dars faqat hozirgi yoki kelajakdagi sanalarda qo'yilishi mumkin.");
                    }

                    $generationSchedule = new GenerationSchedule();
                    $generationSchedule->teacher_id = Auth::id();
                    $generationSchedule->subject_group_id = $data['subject_group_id'];
                    $generationSchedule->date = $date->toDateString();
                    $generationSchedule->pair = $data['pair'];
                    $generationSchedule->save();

                    if ($subjectGroupTeacher) {
                        $generationSchedule = new GenerationSchedule();
                        $generationSchedule->teacher_id = $subjectGroupTeacher->groups->first()->pivot->teacher_id;
                        $generationSchedule->subject_group_id = $data['subject_group_id'];
                        $generationSchedule->date = $date->toDateString();
                        $generationSchedule->pair = $data['pair'];
                        $generationSchedule->save();
                    }

                    $datesForTargetDay[$date->toDateString()][] = $date->toDateString();

                    // Fanni statusini yangilash
                    $subjectGroup->update(['status' => true]);

                    continue; // Ushbu holatda qolgan shartlarni o'tkazib yuborish
                }

                $totalLessons = ceil(($subjectGroup->lesson_hour / 2) / $totalWeeks);
                $totalLessons = max($totalLessons, 1); // Hech bo'lmaganda 1 dars qo'yilishi kerak

                $weeklySchedule = [];
                for ($week = 0; $week <= $totalWeeks; $week++) {
                    $currentWeekDate = $date->copy()->addWeeks($week);

                    if (!$currentWeekDate->between($start_date, $end_date)) {
                        continue;
                    }

                    $weekNumber = $currentWeekDate->weekOfYear;

                    if (!isset($weeklySchedule[$weekNumber])) {
                        $weeklySchedule[$weekNumber] = [];
                    }

                    if (count($weeklySchedule[$weekNumber]) >= $totalLessons) {
                        continue;
                    }

                    $dayOfWeek = $currentWeekDate->dayOfWeek;

                    if (isset($weeklySchedule[$weekNumber][$dayOfWeek])) {
                        throw new Exception(
                            "Haftaning bir kunida faqat bitta dars qo'yilishi mumkin: " . $currentWeekDate->toDateString()
                        );
                    }

                    $generationSchedule = new GenerationSchedule();
                    $generationSchedule->teacher_id = Auth::id();
                    $generationSchedule->subject_group_id = $data['subject_group_id'];
                    $generationSchedule->date = $currentWeekDate->toDateString();
                    $generationSchedule->pair = $data['pair'];
                    $generationSchedule->save();

                    if ($subjectGroupTeacher) {
                        $generationSchedule = new GenerationSchedule();
                        $generationSchedule->teacher_id = $subjectGroupTeacher->groups->first()->pivot->teacher_id;
                        $generationSchedule->subject_group_id = $data['subject_group_id'];
                        $generationSchedule->date = $date->toDateString();
                        $generationSchedule->pair = $data['pair'];
                        $generationSchedule->save();
                    }

                    $weeklySchedule[$weekNumber][$dayOfWeek] = $currentWeekDate->toDateString();

                    $datesForTargetDay[$date->toDateString()][] = $currentWeekDate->toDateString();
                }

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
