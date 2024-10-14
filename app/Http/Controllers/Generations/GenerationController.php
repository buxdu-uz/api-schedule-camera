<?php

namespace App\Http\Controllers\Generations;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\GenerationSchedules\Requests\StoreGenerationScheduleRequest;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\Syllabus\Models\Syllabus;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GenerationController extends Controller
{
    public function getWeeks()
    {
        $syllabus = Syllabus::query()->latest()->first();
        $start_date = Carbon::parse($syllabus->start_date);
        $end_date = Carbon::parse($syllabus->end_date);
        $daysDifference = $start_date->diffInWeeks($end_date);

        $weeksData = [];

        for ($i = 0; $i <= $daysDifference; $i++) {
            $startOfWeek = $start_date->copy()->addWeeks($i)->startOfWeek(); // Get the start of the week
            $endOfWeek = $startOfWeek->copy()->endOfWeek(); // Get the end of the week

            $weeksData[] = [
                'start' => $startOfWeek->toDateString(),
                'end' => $endOfWeek->toDateString(),
            ];
        }

        return $weeksData;
    }

    public function generateSchedules(StoreGenerationScheduleRequest $request)
    {
        $request->validated();
        try {
            $syllabus = Syllabus::query()->latest()->first();
            $start_date = Carbon::parse($syllabus->start_date);
            $end_date = Carbon::parse($syllabus->end_date);
            $daysDifference = $start_date->diffInWeeks($end_date);
            $datesForTargetDay = [];

            foreach ($request->data as $data) {
                $date = Carbon::parse($data['date']);
                $targetDayOfWeek = $date->dayOfWeek;
                // Retrieve the subject group to determine how many times to insert per week
                $subject_group = SubjectGroup::query()->find($data['subject_group_id']);
                $countSubject = round(($subject_group->lesson_hour / 2) / $daysDifference); // Calculate countSubject

                // Initialize an array to track how many times we've inserted for this week
                $weeklyInsertCount = [];

                for ($i = 0; $i <= $daysDifference; $i++) {
                    // Calculate the target day for the week
//                    $weekTargetDay = $date->copy()->addWeeks($i)->startOfWeek()->addDays($targetDayOfWeek);
                    $weekTargetDay = $date->copy()->addWeeks($i);
                    // Check if the target day is within the syllabus date range
                    if ($weekTargetDay->between($start_date, $end_date)) {
                        // Initialize the insert counter for the week if it doesn't exist
                        $weekNumber = $weekTargetDay->weekOfYear; // Get the week number for tracking
                        if (!isset($weeklyInsertCount[$weekNumber])) {
                            $weeklyInsertCount[$weekNumber] = 0; // Initialize to zero
                        }
                        // Check if we can still insert for this week
                        if ($weeklyInsertCount[$weekNumber] < $countSubject) {
                            $generationSchedule = new GenerationSchedule();
                            $generationSchedule->teacher_id = Auth::id();
                            $generationSchedule->subject_group_id = $data['subject_group_id'];
                            $generationSchedule->room_id = $data['room_id'];
                            $generationSchedule->date = $weekTargetDay->toDateString();
                            $generationSchedule->start_at = $data['start_at'];
                            $generationSchedule->end_at = $data['end_at'];
                            $generationSchedule->pair = $data['pair'];
                            $generationSchedule->save();

                            // Increment the insertion count for this week
                            $weeklyInsertCount[$weekNumber]++;

                            // Store the inserted date
                            $datesForTargetDay[$date->toDateString()][] = $weekTargetDay->toDateString();
                        }
                    }
                }
            }
            return $this->successResponse('Sizning fanlaringiz '.$request->data[0]['pair'].' - semester uchun biriktirildi');
        }catch (Exception $exception){
            return $this->errorResponse($exception->getMessage());
        }
    }
}
