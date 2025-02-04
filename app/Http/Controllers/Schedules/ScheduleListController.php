<?php

namespace App\Http\Controllers\Schedules;

use App\Domain\Schedules\Resources\ScheduleResource;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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


    public function getWeeklySchedule(Req $request)
    {
        $current_week_start = Carbon::now()->startOfWeek()->timestamp;
        $current_week_end = Carbon::now()->endOfWeek()->timestamp;

        $request->validate([
            'room_id' => 'sometimes',
        ]);

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];

        $base_url = config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit') .
            '&lesson_date_from=' . $current_week_start .
            '&lesson_date_to=' . $current_week_end .
            ($request->room_id ? '&_auditorium=' . $request->room_id : '');

        // API dan barcha sahifalarni yuklash
        $data = collect();
        $page = 1;
        do {
            $response = $client->get($base_url . '&page=' . $page, ['headers' => $headers]);
            $resBody = json_decode($response->getBody());

            if (isset($resBody->data->items)) {
                $data = $data->merge($resBody->data->items);
            }
            $pageCount = $resBody->data->pagination->pageCount ?? 1;
            $page++;
        } while ($page <= $pageCount);

        if ($data->isEmpty()) {
            return $this->successResponse('', []);
        }

        // Faqat joriy haftaga tegishli darslarni olish
        $currentWeekLessons = $data->filter(fn($lesson) =>
            isset($lesson->lesson_date, $lesson->educationYear->current) &&
            Carbon::parse($lesson->lesson_date)->between(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()) &&
            $lesson->educationYear->current
        );

        if ($request->room_id) {
            $currentWeekLessons = $currentWeekLessons->where('auditorium.code', $request->room_id);
        }

        $groupedData = [];

        foreach ($currentWeekLessons as $lesson) {
            $date = Carbon::parse($lesson->lesson_date)->format('Y-m-d');
            $building = $lesson->auditorium->building->name ?? 'Unknown Building';
            $room = $lesson->auditorium->name ?? 'Unknown Room';
            $timeSlot = isset($lesson->lessonPair->start_time, $lesson->lessonPair->end_time)
                ? Carbon::parse($lesson->lessonPair->start_time)->format('H:i') . ' - ' .
                Carbon::parse($lesson->lessonPair->end_time)->format('H:i')
                : '-';

            $status = isset($lesson->lessonPair) ? '+' : '-';

            $groupedData[$date][$building][$room][$timeSlot] = $status;
        }

        // **Tartiblash**
        $sortedData = collect($groupedData)->sortKeys()->map(function ($buildings) {
            return collect($buildings)->map(function ($rooms) {
                return collect($rooms)->map(function ($times) {
                    return collect($times)->sortKeys()->toArray();
                })->toArray();
            })->toArray();
        })->toArray();

        return $this->successResponse('', $sortedData);
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
