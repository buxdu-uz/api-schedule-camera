<?php

namespace App\Http\Controllers\Groups;

use App\Domain\Groups\Resources\GroupResource;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function getAllGroup()
    {
        $data = collect();
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];

        // First request to get the initial data and pagination information
        $request_api = new Request(
            'GET',
            config('hemis.host') . 'data/group-list?limit=' . config('hemis.limit'),
            $headers
        );
        $res = $client->sendAsync($request_api)->wait();
        $resBody = json_decode($res->getBody());

        // Check if the request was successful
        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            // Add items from the first page
            foreach ($resBody->data->items as $dt) {
                $data->push([
                    'id' => $dt->id,
                    'name' => $dt->name
                ]);
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/group-list?limit=' . config('hemis.limit') . '&page=' . $i,
                    $headers
                );
                $res = $client->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        $data->push([
                            'id' => $dt->id,
                            'name' => $dt->name
                        ]);
                    }
                }
            }
        }
        return $data;
    }
}
