<?php

namespace Database\Seeders;

use App\Domain\Classifiers\Models\ClassifierOption;
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
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer '.config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request('GET', config('hemis.host').'data/department-list?limit='.config('hemis.limit'), $headers);
        $res = $client->sendAsync($request)->wait();
        $res=$res->getBody();
        $result=json_decode($res);
        if($result->success===true) {
            foreach (collect($result->data->items)->sortBy('id') as $item) {
                DB::beginTransaction();
                try {
                    Department::updateOrCreate([
                        'id' => $item->id,
                    ],[
                        'parent_id' => $item->parent,
                        'name' => $item->name,
                        'code' => $item->code,
                        'h_structure_type' => ClassifierOption::getId('structureType', $item->structureType->code),
                        'h_locality_type' => ClassifierOption::getId('localityType', $item->localityType->code),
                    ]);
                    DB::commit();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    throw $exception;
                }
            }
        }
    }
}
