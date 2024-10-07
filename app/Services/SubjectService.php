<?php

namespace App\Services;


use App\Domain\Buildings\Models\Building;
use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Subjects\Models\Subject;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\DB;

class SubjectService
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
            config('hemis.host') . 'data/subject-meta-list?limit=' . config('hemis.limit'),
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
                            config('hemis.host') . 'data/subject-meta-list?limit=' . config(
                                'hemis.limit'
                            ) . '&page=' . $i,
                            $headers
                        );
                        $res = $client->sendAsync($request)->wait();
                        $res = $res->getBody();
                        $result = json_decode($res);
                        $this->store($result);
                    }
                    echo '    Subject metas page: ' . $i . '/' . $result->data->pagination->pageCount . ' Stored' . PHP_EOL;
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
            dd(ClassifierOption::getId('subject_block', $item->subjectGroup->code),$item->subjectGroup->code);
            DB::beginTransaction();
            try {
                // Check if the building exists or needs to be updated
                Subject::updateOrCreate([
                    'id' => $item->id,
                ], [
                    'code' => $item->code,
                    'name' => $item->name,
                    'active' => $item->active,
                    'h_subject_block' => ClassifierOption::getId('subject_block', $item->subjectGroup->code),
                    'h_education_type' => ClassifierOption::getId('h_education_type', $item->educationType->code),
                ]);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
        }
    }
}
