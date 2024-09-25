<?php

namespace Database\Seeders;

use App\Domain\Departments\Models\Department;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=collect();
        echo "Starting Migrate Hemis Departments" . PHP_EOL;
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer '.config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request('GET', config('hemis.host').'data/department-list?limit='.config('hemis.limit').'&active=1', $headers);
        $res = $client->sendAsync($request)->wait();
        $resBody = json_decode($res->getBody());

        // Check if the request was successful
        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            // Add items from the first page
            foreach ($resBody->data->items as $dt) {
                Department::updateOrCreate([
                    'id' => $dt->id,
                ],[
                    'parent_id' => $dt->parent,
                    'name' => $dt->name,
                    'code' => $dt->code,
                ]);
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/department-list?limit='.config('hemis.limit').'&active=1&page=' . $i,
                    $headers
                );
                $res = $client->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        Department::updateOrCreate([
                            'id' => $dt->id,
                        ],[
                            'parent_id' => $dt->parent,
                            'name' => $dt->name,
                            'code' => $dt->code,
                        ]);
                    }
                }
            }
        }
//        $res=$res->getBody();
//        $result=json_decode($res);
//        if($result->success===true) {
//            foreach (collect($result->data->items)->sortBy('id') as $item) {
//                DB::beginTransaction();
//                try {
//                    Department::updateOrCreate([
//                        'id' => $item->id,
//                    ],[
//                        'parent_id' => $item->parent,
//                        'name' => $item->name,
//                        'code' => $item->code,
//                    ]);
//                    DB::commit();
//                } catch (\Exception $exception) {
//                    DB::rollBack();
//                    throw $exception;
//                }
//            }
//        }
    }
}
