<?php

namespace App\Http\Controllers\Schedules;

use App\Domain\Schedules\Resources\ScheduleResource;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
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

        // 1. API-dan birinchi sahifani olish va sahifalar sonini aniqlash
        $response = $client->get($base_url . '&page=1', ['headers' => $headers]);
        $resBody = json_decode($response->getBody());

        if (!isset($resBody->data->items)) {
            return $this->successResponse('', []);
        }

        $pageCount = $resBody->data->pagination->pageCount ?? 1;

        // 2. Paralel so‘rovlar (Guzzle's async requests)
        $promises = [];
        for ($page = 2; $page <= $pageCount; $page++) {
            $promises[$page] = $client->getAsync($base_url . '&page=' . $page, ['headers' => $headers]);
        }

        $responses = Utils::settle($promises)->wait();

        // 3. Ma'lumotlarni yig'ish va guruhlash
        $data = collect($resBody->data->items);

        foreach ($responses as $response) {
            if ($response['state'] === 'fulfilled') {
                $resData = json_decode($response['value']->getBody());
                if (isset($resData->data->items)) {
                    $data = $data->merge($resData->data->items);
                }
            }
        }

        if ($data->isEmpty()) {
            return $this->successResponse('', []);
        }

        // 4. Faqat kerakli ma'lumotlarni filter qilish
        $groupedData = [];

        foreach ($data as $lesson) {
            if (!isset($lesson->lesson_date, $lesson->educationYear->current) ||
                !Carbon::parse($lesson->lesson_date)->between(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()) ||
                !$lesson->educationYear->current
            ) {
                continue;
            }

            if ($request->room_id && ($lesson->auditorium->code ?? '') !== $request->room_id) {
                continue;
            }

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
        $sortedData = collect($groupedData)->sortKeys()->map(fn($buildings) =>
        collect($buildings)->map(fn($rooms) =>
        collect($rooms)->map(fn($times) => collect($times)->sortKeys()->toArray())->toArray()
        )->toArray()
        )->toArray();

        return $this->successResponse('', $sortedData);
    }

    public function getDailySchedule(Req $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'lesson_date_from' => 'required|date',
            'lesson_date_to' => 'required|date',
        ]);

        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];

        $base_url = config('hemis.host') . 'data/schedule-list?limit=' . config('hemis.limit') .
            '&_employee=' . $request->employee_id .
            '&lesson_date_from=' . Carbon::parse($request->lesson_date_from)->startOfDay()->timestamp .
            '&lesson_date_to='   . Carbon::parse($request->lesson_date_to)->endOfDay()->timestamp;

        // 1. API-dan birinchi sahifani olish va sahifalar sonini aniqlash
        $response = $client->get($base_url . '&page=1', ['headers' => $headers]);
        $resBody = json_decode($response->getBody());

        if (!isset($resBody->data->items)) {
            return $this->successResponse('', []);
        }

        $pageCount = $resBody->data->pagination->pageCount ?? 1;

        // 2. Paralel so‘rovlar
        $promises = [];
        for ($page = 2; $page <= $pageCount; $page++) {
            $promises[$page] = $client->getAsync($base_url . '&page=' . $page, ['headers' => $headers]);
        }

        $responses = Utils::settle($promises)->wait();

        // 3. Ma'lumotlarni yig'ish
        $data = collect($resBody->data->items);

        foreach ($responses as $response) {
            if ($response['state'] === 'fulfilled') {
                $resData = json_decode($response['value']->getBody());
                if (isset($resData->data->items)) {
                    $data = $data->merge($resData->data->items);
                }
            }
        }

        if ($data->isEmpty()) {
            return $this->successResponse('', []);
        }

        // 4. Sana bo‘yicha filter (lesson_date_from ~ lesson_date_to)
        $groupedData = [];
        foreach ($data as $lesson) {
            if (!isset($lesson->lesson_date, $lesson->educationYear->current) || !$lesson->educationYear->current) {
                continue;
            }

            $lessonDate = Carbon::parse($lesson->lesson_date);

            $date = $lessonDate->format('Y-m-d');
            $building = $lesson->auditorium->building->name ?? 'Unknown Building';
            $subject  = $lesson->subject->name ?? 'Unknown Subject';
            $group    = $lesson->group->name ?? 'Unknown Group';

            $timeSlot = isset($lesson->lessonPair->start_time, $lesson->lessonPair->end_time)
                ? Carbon::parse($lesson->lessonPair->start_time)->format('H:i') . ' - ' .
                Carbon::parse($lesson->lessonPair->end_time)->format('H:i')
                : '-';

            $status = isset($lesson->lessonPair) ? '+' : '-';

            $groupedData[$date][] = [
                'bino'    => $building,
                'subject' => $subject,
                'group'   => $group,
                'pair'    => $timeSlot,
                'status'  => $status,
            ];
        }

// 5. Tartiblash (sana bo‘yicha sort)
        $sortedData = collect($groupedData)->sortKeys()->toArray();

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
