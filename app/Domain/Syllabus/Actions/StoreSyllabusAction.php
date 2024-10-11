<?php

namespace App\Domain\Syllabus\Actions;

use App\Domain\Syllabus\DTO\StoreSyllabusDTO;
use App\Domain\Syllabus\Models\Syllabus;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreSyllabusAction
{
    /**
     * @param StoreSyllabusDTO $dto
     * @return Syllabus
     * @throws Exception
     */
    public function execute(StoreSyllabusDTO $dto): Syllabus
    {
        DB::beginTransaction();
        try {
            $syllabus = new Syllabus();
            $syllabus->semester = $dto->getSemester();
            $syllabus->start_date = $dto->getStartDate();
            $syllabus->end_date = $dto->getEndDate();
            $syllabus->save();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $syllabus;
    }
}
