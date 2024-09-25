<?php

namespace App\Http\Controllers\Schedules;

use App\Domain\Schedules\Resources\ScheduleResource;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\Request as Req;

class ScheduleListController extends Controller
{
    public function getScheduleListHemis(Req $request)
    {
        $current_week_start =Carbon::now()->startOfWeek()->timestamp;
        $current_week_end = Carbon::now()->endOfWeek()->timestamp;


//        $rawWeekNumber = 181600;
//        $weekNumber = substr($rawWeekNumber, 0, 2); // Get the first two digits (for example, '18')
//        $year = 2024; // Assuming a specific year
//        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
//        $endOfWeek = $startOfWeek->copy()->endOfWeek();
//        dd($startOfWeek,$endOfWeek);

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
//        if ($result->success === true) {
//            if ($result->data->pagination->totalCount > config('hemis.limit')) {
//                for ($i = 1; $i <= $result->data->pagination->pageCount; $i++) {
//                        $request_api = new Request(
//                            'GET',
//                            config('hemis.host') . 'data/schedule-list?page='.$i.'&limit=' . config('hemis.limit').'&lesson_date_from='.$current_week_start.'&lesson_date_to='.$current_week_end.'&_group=' . $request->group_id . '&_employee='.$request->employee_id.'&_auditorium='.$request->room_id,
//                            $headers
//                        );
//                        $res = $client->sendAsync($request_api)->wait();
//                        $res = $res->getBody();
//                        $result = json_decode($res);
////                        dd($result->data);
////                        foreach ($result->data as $data){
////                            dd($data);
////                        }
//
//                }
//            }
//        } else {
////            $this->store($result);
//        }
        return $this->successResponse('',ScheduleResource::collection($result->data->items));
    }

}
