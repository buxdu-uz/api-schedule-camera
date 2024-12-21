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

            foreach ($dto->getData() as $data) {
                $syllabus = Syllabus::query()->find($data['syllabi_id']);
                $start_date = Carbon::parse($syllabus->start_date);
                $end_date = Carbon::parse($syllabus->end_date);
                $totalWeeks = $start_date->diffInWeeks($end_date);
                $datesForTargetDay = [];
                $date = Carbon::parse($data['date']);
                $subjectGroup = SubjectGroup::query()->find($data['subject_group_id']);

                $subjectGroupTeacher = $subjectGroup->whereHas('groups', function ($query) {
                    $query->whereNotNull('teacher_id'); // More explicit than '!=', ensuring null safety.
                })->first(); // Add 'first()' or 'get()' depending on your intention.

                if (!$subjectGroup) {
                    throw new Exception('Mavjud subject_group_id topilmadi.');
                }

                // Calculate total lessons per week
                $totalLessons = ceil(($subjectGroup->lesson_hour / 2) / $totalWeeks);

                // Special case: if totalLessons is less than 1, schedule the lesson on the specified date
                if ((($subjectGroup->lesson_hour / 2) / $totalWeeks) < 1) {
                    $totalLessons = 1; // Ensure at least one lesson
                    $weekNumber = $date->weekOfYear;

                    if (!isset($weeklySchedule[$weekNumber])) {
                        $weeklySchedule[$weekNumber] = [];
                    }

                    $dayOfWeek = $date->dayOfWeek;

                    // Prevent scheduling on past dates
                    if ($date->isPast()) {
                        throw new Exception("Dars faqat hozirgi yoki kelajakdagi sanalarda qo'yilishi mumkin.");
                    }

                    // Check if the lesson already exists on the same day
                    if (isset($weeklySchedule[$weekNumber][$dayOfWeek])) {
                        throw new Exception(
                            "Haftaning bir kunida faqat bitta dars qo'yilishi mumkin: " . $date->toDateString()
                        );
                    }

                    // Jadvalga dars qo'shish
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

                    // Haftalik jadvalni yangilash
                    $weeklySchedule[$weekNumber][$dayOfWeek] = $date->toDateString();

                    // Qo'shilgan sanani saqlash
                    $datesForTargetDay[$date->toDateString()][] = $date->toDateString();

                    // Skip the rest of the loop for this special case
                    continue;
                }

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

                    if ($subjectGroupTeacher) {
                        $generationSchedule = new GenerationSchedule();
                        $generationSchedule->teacher_id = $subjectGroupTeacher->groups->first()->pivot->teacher_id;
                        $generationSchedule->subject_group_id = $data['subject_group_id'];
                        $generationSchedule->date = $date->toDateString();
                        $generationSchedule->pair = $data['pair'];
                        $generationSchedule->save();
                    }

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
