<?php

namespace App\Http\Controllers\Schedules;

use App\Domain\Schedules\Resources\ScheduleResource;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Auth;

class ScheduleListController extends Controller
{
    public function getScheduleListHemis(Req $request)
    {
        $data = collect();
        $current_week_start =Carbon::now()->startOfWeek()->timestamp;
        $current_week_end = Carbon::now()->endOfWeek()->timestamp;

        $request->validate([
            'group_id' => 'sometimes',
            'employee_id' => 'sometimes',
            'room_id' => 'sometimes',
        ]);
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request_api = new Request(
            'GET',
            config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit').'&lesson_date_from='.$current_week_start.'&lesson_date_to='.$current_week_end.'&_group=' . $request->group_id . '&_employee='.$request->employee_id.'&_auditorium='.$request->room_id,
            $headers
        );
        $res = $client->sendAsync($request_api)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
//        dd($result->data);
        // Check if the request was successful
        if (isset($result->data->pagination->pageCount) && isset($result->data->items)) {
            // Add items from the first page
            foreach ($result->data->items as $dt) {
                $data->push($dt);
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $result->data->pagination->pageCount; $i++) {
                $request_api = new Request(
                    'GET',
                    config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit').'&lesson_date_from='.$current_week_start.'&lesson_date_to='.$current_week_end.'&_group=' . $request->group_id . '&_employee='.$request->employee_id.'&_auditorium='.$request->room_id.'&page=' . $i,
                    $headers
                );
                $res = $client->sendAsync($request_api)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        $data->push($dt);
                    }
                }
            }
        }
        $data = $data->sortBy('lessonPair.start_time');
        return $this->successResponse('',ScheduleResource::collection($data));
    }


    public function getScheduleListHemisRooms(Req $request)
    {
        $data = collect();

        // Validate request for room_id (optional)
        $request->validate([
            'room_id' => 'sometimes',
        ]);

        // Set up the client and headers for API request
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];

        // Fetch data from API
        $page = 1;
        $weekStartTime = null;
        $weekEndTime = null;

        do {
            $request_api = new Request(
                'GET',
                config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit') .
                '&page=' . $page,
                $headers
            );

            // Send the request asynchronously and wait for response
            $res = $client->sendAsync($request_api)->wait();
            $resBody = json_decode($res->getBody());

            // Extract week start and end times from the API response (assuming these are provided)
            if (!$weekStartTime && isset($resBody->data->weekStartTime)) {
                $weekStartTime = Carbon::parse($resBody->data->weekStartTime)->format('Y-m-d'); // Start of week (from API)
            }
            if (!$weekEndTime && isset($resBody->data->weekEndTime)) {
                $weekEndTime = Carbon::parse($resBody->data->weekEndTime)->format('Y-m-d'); // End of week (from API)
            }

            // Check and collect the schedule items
            if (isset($resBody->data->items)) {
                foreach ($resBody->data->items as $dt) {
                    $data->push($dt);
                }
            }

            $page++;
        } while (isset($resBody->data->pagination->pageCount) && $page <= $resBody->data->pagination->pageCount);

        // Validate that weekStartTime and weekEndTime are available
        if (!$weekStartTime || !$weekEndTime) {
            return $this->errorResponse('Week start and end times are not provided.');
        }

        // Group lessons by date (within the week range)
        $grouped = $data->filter(function ($lesson) use ($weekStartTime, $weekEndTime) {
            $lesson_date = Carbon::parse($lesson->lessonPair->start_time)->format('Y-m-d');
            return $lesson_date >= $weekStartTime && $lesson_date <= $weekEndTime;
        })->groupBy(function ($lesson) {
            return Carbon::parse($lesson->lessonPair->start_time)->format('Y-m-d'); // Example: 2025-01-27
        });

        // Group by buildings and rooms, and sort by times
        $result = $grouped->map(function ($lessons, $date) {
            $buildings = [];
            foreach ($lessons as $lesson) {
                $building_name = $lesson->auditorium->building->name; // Building name
                $room_name = $lesson->auditorium->name; // Room name
                $start_time = Carbon::parse($lesson->lessonPair->start_time); // Start time
                $end_time = Carbon::parse($lesson->lessonPair->end_time); // End time

                // Ensure the building and room exist in the data structure
                if (!isset($buildings[$building_name])) {
                    $buildings[$building_name] = [];
                }

                if (!isset($buildings[$building_name][$room_name])) {
                    $buildings[$building_name][$room_name] = [];
                }

                // Track the times for each room
                $buildings[$building_name][$room_name][$start_time->format('H:i')] = '+';
                $buildings[$building_name][$room_name][$end_time->format('H:i')] = '-';
            }

            // Sort times in each room and ensure they are in order
            foreach ($buildings as $building => $rooms) {
                foreach ($rooms as $room => $times) {
                    ksort($times); // Sort times in ascending order
                    $buildings[$building][$room] = $times;
                }
            }

            return [
                'date' => $date,
                'buildings' => $buildings
            ];
        });

        // Return the formatted data as response
        return $this->successResponse('', $result);
    }





    public function userSchedule(Req $request)
    {
        $data = collect();
        $current_week_start =Carbon::now()->startOfWeek()->timestamp;
        $current_week_end = Carbon::now()->endOfWeek()->timestamp;

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request_api = new Request(
            'GET',
            config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit').'&lesson_date_from='.$current_week_start.'&lesson_date_to='.$current_week_end.'&_group=' . $request->group_id . '&_employee='.Auth::user()->id.'&_auditorium='.$request->room_code,
            $headers
        );
        $res = $client->sendAsync($request_api)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        // Check if the request was successful
        if (isset($result->data->pagination->pageCount) && isset($result->data->items)) {
            // Add items from the first page
            foreach ($result->data->items as $dt) {
                $data->push($dt);
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $result->data->pagination->pageCount; $i++) {
                $request_api = new Request(
                    'GET',
                    config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit').'&lesson_date_from='.$current_week_start.'&lesson_date_to='.$current_week_end.'&_group=' . $request->group_id . '&_employee='.Auth::user()->id.'&_auditorium='.$request->room_code.'&page=' . $i,
                    $headers
                );
                $res = $client->sendAsync($request_api)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        $data->push($dt);
                    }
                }
            }
        }
        $data = $data->sortBy('lessonPair.start_time');
        return $this->successResponse('',ScheduleResource::collection($data));
    }

}
