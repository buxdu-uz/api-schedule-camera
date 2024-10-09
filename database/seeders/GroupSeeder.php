<?php

namespace Database\Seeders;

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Groups\Models\Group;
use App\Domain\Specialities\Models\Speciality;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws \Throwable
     */
    public function run(): void
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request('GET', config('hemis.host') . 'data/group-list?limit=' . config('hemis.limit'), $headers);
        $res = $client->sendAsync($request)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        if ($result->success === true) {
            if ($result->data->pagination->totalCount > config('hemis.limit')) {
                for ($i = 1; $i <= $result->data->pagination->pageCount; $i++) {
                    if ($i === 1) {
                        $this->store($result);
                    } else {
                        $request = new Request('GET', config('hemis.host') . 'data/group-list?limit=' . config('hemis.limit') . '&page=' . $i, $headers);
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Group page: '.$i.'/'.$result->data->pagination->pageCount.' Stored'.PHP_EOL;
                }
            }
        } else {
            $this->store($result);
        }
    }

    /**
     * Store group array
     * @throws \Throwable
     */
    public function store($result): void
    {
        foreach (collect($result->data->items)->sortBy('id') as $item) {
            DB::beginTransaction();
            try {
                if($item->specialty->id == 17){
                    $speciality_id = 109;
                }elseif($item->specialty->id == 179){
                    $speciality_id = 163;
                }else{
                    $speciality_id = $item->specialty->id;
                }

                Group::updateOrCreate([
                    'id' => $item->id,
                ], [
                    'name' => $item->name,
                    'department_id' => $item->department->id,
                    'h_language' => ClassifierOption::getId('language', $item->educationLang->code),
                    'speciality_id' => $speciality_id
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
