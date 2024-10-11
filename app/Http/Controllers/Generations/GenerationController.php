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
}
