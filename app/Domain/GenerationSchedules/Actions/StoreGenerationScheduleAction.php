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
            $daysDifference = $start_date->diffInWeeks($end_date);
            $datesForTargetDay = [];

            foreach ($dto->getData() as $data) {
                $date = Carbon::parse($data['date']);
                // Retrieve the subject group to determine how many times to insert per week
                $subject_group = SubjectGroup::query()->find($data['subject_group_id']);
                $countSubject = round(($subject_group->lesson_hour / 2) / $daysDifference); // Calculate countSubject

                // Initialize an array to track how many times we've inserted for this week
                $weeklyInsertCount = [];

                for ($i = 0; $i <= $daysDifference; $i++) {
                    // Calculate the target day for the week
                    $weekTargetDay = $date->copy()->addWeeks($i);
                    // Check if the target day is within the syllabus date range
                    if ($weekTargetDay->between($start_date, $end_date)) {
//                    dd($weekTargetDay);
                        // Initialize the insert counter for the week if it doesn't exist
                        $weekNumber = $weekTargetDay->weekOfYear; // Get the week number for tracking

                        if (!isset($weeklyInsertCount[$weekNumber])) {
                            $weeklyInsertCount[$weekNumber] = 0; // Initialize to zero
                        }
                        // Check if we can still insert for this week
                        if ($weeklyInsertCount[$weekNumber] <= $countSubject) {
                            $generationSchedule = new GenerationSchedule();
                            $generationSchedule->teacher_id = Auth::id();
                            $generationSchedule->subject_group_id = $data['subject_group_id'];
                            $generationSchedule->date = $weekTargetDay->toDateString();
                            $generationSchedule->pair = $data['pair'];
                            $generationSchedule->save();

                            // Increment the insertion count for this week
                            $weeklyInsertCount[$weekNumber]++;

                            // Store the inserted date
                            $datesForTargetDay[$date->toDateString()][] = $weekTargetDay->toDateString();
                        }else{
                            throw new Exception('Siz ushbu fanni faqat 1 haftaga faqat '. $countSubject. ' marta qoyishingiz mumkin.');
                        }
                    }
                }
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $datesForTargetDay;
    }
}
