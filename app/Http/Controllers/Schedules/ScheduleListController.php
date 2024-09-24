<?php

namespace App\Http\Controllers\Schedules;

use App\Domain\Schedules\Resources\ScheduleResource;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ScheduleListController extends Controller
{
    public function getScheduleListHemis(Request $request)
    {
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
        $request = new \GuzzleHttp\Psr7\Request(
            'GET',
            config('hemis.host') . 'data/schedule-list?_group=' . $request->group_id . '&_employee='.$request->employee_id.'&_auditorium='.$request->room_id,
            $headers
        );
        $res = $client->sendAsync($request)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        return $this->successResponse('',ScheduleResource::collection($result->data->items));
    }

}
