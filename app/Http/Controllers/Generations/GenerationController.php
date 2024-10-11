<?php

namespace App\Http\Controllers\Generations;

use App\Domain\Syllabus\Models\Syllabus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GenerationController extends Controller
{
    public function getWeeks()
    {
        // Get the current date or specify a starting date
        $startDate = Carbon::parse(Syllabus::query()->latest()->first()->start_date);
        $endDate = Carbon::parse(Syllabus::query()->latest()->first()->end_date);
        $weeksData = [];

        // Loop through the next 15 weeks
        for ($i = 0; $i < 15; $i++) {
            $startOfWeek = $startDate->copy()->addWeeks($i)->startOfWeek(); // Get the start of the week
            $endOfWeek = $endDate->endOfWeek(); // Get the end of the week

            $weeksData[] = [
                'start' => $startOfWeek->toDateString(), // Format to date string (YYYY-MM-DD)
                'end' => $endOfWeek->toDateString(), // Format to date string (YYYY-MM-DD)
            ];
        }

        // Output the weeks data
        return $weeksData;
    }
}
