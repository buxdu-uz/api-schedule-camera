<?php

namespace App\Services;


use App\Domain\Buildings\Models\Building;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    /**
     * @throws \Throwable
     */
    public function hemisMigration(): void
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request(
            'GET',
            config('hemis.host') . 'data/auditorium-list?limit=' . config('hemis.limit'),
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
                            config('hemis.host') . 'data/auditorium-list?limit=' . config(
                                'hemis.limit'
                            ) . '&page=' . $i,
                            $headers
                        );
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Buildings page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
                }
            }
        } else {
            $this->store($result);
        }
    }

    /**
     * @throws \Throwable
     */
    public function store($result): void
    {
        foreach (collect($result->data->items)->sortBy('id') as $item) {
//
            DB::beginTransaction();
            try {
                // Check if the building exists or needs to be updated
                $building = Building::updateOrCreate([
                    'id' => $item->building->id,
                ], [
                    'name' => $item->building->name,
                ]);

                $building->rooms()->updateOrCreate([
                    'code' => $item->code,
                ], [
                    'name' => $item->name,
                ]);
                DB::commit();
            } catch (\Exception $exception) {
                echo json_encode($item);
                DB::rollBack();
                throw $exception;
            }
        }
    }
}
