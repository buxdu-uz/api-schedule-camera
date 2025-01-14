<?php

namespace App\Domain\Syllabus\Actions;

use App\Domain\Syllabus\DTO\StoreSyllabusDTO;
use App\Domain\Syllabus\DTO\UpdateSyllabusDTO;
use App\Domain\Syllabus\Models\Syllabus;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateSyllabusAction
{
    /**
     * @param UpdateSyllabusDTO $dto
     * @return Syllabus
     * @throws Exception
     */
    public function execute(UpdateSyllabusDTO $dto): Syllabus
    {
        DB::beginTransaction();
        try {
            $syllabus = $dto->getSyllabus();
            $syllabus->semester = $dto->getSemester();
            $syllabus->start_date = $dto->getStartDate();
            $syllabus->end_date = $dto->getEndDate();
            $syllabus->update();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $syllabus;
    }
}
