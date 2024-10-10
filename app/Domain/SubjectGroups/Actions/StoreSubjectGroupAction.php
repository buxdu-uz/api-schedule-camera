<?php

namespace App\Domain\SubjectGroups\Actions;

use App\Domain\SubjectGroups\DTO\StoreSubjectGroupDTO;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Enums\FlowOrSplitGroup;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreSubjectGroupAction
{
    /**
     * @param StoreSubjectGroupDTO $dto
     * @return SubjectGroup
     * @throws Exception
     */
    public function execute(StoreSubjectGroupDTO $dto): SubjectGroup
    {
        DB::beginTransaction();
        try {
            foreach ($dto->getData() as $key => $data) {
                $subject_group = new SubjectGroup();
                $subject_group->teacher_id = Auth::id();
                $subject_group->subject_id = $data['subject_id'];
                $subject_group->lesson = $data['lesson'];
                $subject_group->flow = FlowOrSplitGroup::from($data['flow']);
                $subject_group->split_group = FlowOrSplitGroup::from($data['split_group']);
                $subject_group->lesson_hour = $data['lesson_hour'];
                $subject_group->h_education_year = $data['education_year'];
                $subject_group->semester = $data['semester'];
                $subject_group->save();
                $subject_group->groups()->attach($data['groups']);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $subject_group;
    }
}
