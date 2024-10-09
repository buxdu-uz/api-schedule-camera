<?php

namespace Database\Seeders;
use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Specialities\Models\Speciality;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitySeeder extends Seeder
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
        $request = new Request('GET', config('hemis.host').'data/specialty-list?limit='.config('hemis.limit'), $headers);
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
                            config('hemis.host') . 'data/specialty-list?limit=' . config(
                                'hemis.limit'
                            ) . '&page=' . $i,
                            $headers
                        );
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Speciality page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
                }
            }
        }else {
            $this->store($result);
        }
    }

    public function store($result): void
    {
        foreach (collect($result->data->items)->sortBy('id') as $item) {
            DB::beginTransaction();
            try {
                Speciality::updateOrCreate([
                    'id' => $item->id,
                ], [
                    'code' => $item->code,
                    'name' => $item->name,
                    'department_id' => $item->department->id,
                    'h_locality_type' => ClassifierOption::getId('localityType', $item->localityType->code),
                    'h_education_type' => ClassifierOption::getId('educationType', $item->educationType->code),
                    'bachelor_specialty' => $item->bachelorSpecialty != null ? $item->bachelorSpecialty->code : null,
                    'master_specialty' => $item->masterSpecialty != null ? $item->masterSpecialty->code : null,
                    'doctorate_specialty' => $item->doctorateSpecialty != null ? $item->doctorateSpecialty->code : null,
                    'ordinature_specialty' => $item->ordinatureSpecialty != null ? $item->ordinatureSpecialty->code : null,
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
