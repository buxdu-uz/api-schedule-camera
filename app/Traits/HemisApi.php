<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

trait HemisApi
{
    public function connect($url,string $type=null)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request(
            'GET',
            config('hemis.host') . 'data/'.$url.'?type=' . $type . '&limit=' . config('hemis.limit'),
            $headers
        );
        $res = $client->sendAsync($request)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        if ($result->success === true) {
            if ($result->data->pagination->totalCount > config('hemis.limit')) {
                for ($i = 1; $i <= $result->data->pagination->pageCount; $i++) {
                    if ($i === 1) {
                        $this->store($result);
                    } else {
                        $request = new Request(
                            'GET',
                            config('hemis.host') . 'data/'.$url.'?type=' . $type . '&limit=' . config(
                                'hemis.limit'
                            ) . '&page=' . $i,
                            $headers
                        );
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Employeds page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
                }
            }
        } else {
            $this->store($result);
        }
    }

    public function store($result)
    {
        //
    }
}
