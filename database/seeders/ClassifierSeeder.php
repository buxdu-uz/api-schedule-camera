<?php

namespace Database\Seeders;


use App\Domain\Classifiers\Models\Classifier;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws \Throwable
     */
    public function run(): void
    {
//        dd('work');
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer '.config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
        $request = new Request('GET', config('hemis.host').'data/classifier-list?limit='.config('hemis.limit'), $headers);
        $res = $client->sendAsync($request)->wait();
        $res = $res->getBody();
        $result = json_decode($res);
        if ($result->success === true) {
//            dd($result->data->pagination);
//            if ($result->data->pagination->totalCount > config('hemis.limit')) {
//            dd('work');
                for ($i = 1; $i <= $result->data->pagination->pageCount; $i++) {
                    if ($i === 1) {
                        $this->store($result);
                    } else {
                        $request = new Request('GET', config('hemis.host').'data/classifier-list?limit='.config('hemis.limit').'&page='.$i, $headers);
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Classifier page: '.$i.'/'.$result->data->pagination->pageCount.' Stored'.PHP_EOL;
                }
//            }
        } else {
            $this->store($result);
        }
    }

    public function store($result): void
    {
        foreach (collect($result->data->items)->sortBy('id') as $item) {
            DB::beginTransaction();
            try {
                $classifier = Classifier::updateOrCreate([
                    'classifier' => $item->classifier,
                ], [
                    'name' => $item->name,
                    'version' => $item->version < 1 ? 1 : $item->version,
                ]);
                foreach ($item->options as $option) {
                    $classifier->options()->updateOrCreate([
                        'code' => $option->code,
                    ], [
                        'name' => $option->name,
                    ]);
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
        }
    }
}
