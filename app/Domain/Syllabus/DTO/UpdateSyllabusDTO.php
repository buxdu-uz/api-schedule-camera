<?php

namespace App\Domain\Syllabus\DTO;

use App\Domain\Syllabus\Models\Syllabus;

class UpdateSyllabusDTO
{
    /**
     * @var int
     */
    private int $semester;

    /**
     * @var string
     */
    private string $start_date;

    /**
     * @var string
     */
    private string $end_date;

    /**
     * @var Syllabus
     */
    private Syllabus $syllabus;

    /**
     * @param array $data
     * @return UpdateSyllabusDTO
     */
    public static function fromArray(array $data): UpdateSyllabusDTO
    {
        $dto = new self();
        $dto->setSemester($data['semester']);
        $dto->setStartDate($data['start_date']);
        $dto->setEndDate($data['end_date']);
        $dto->setSyllabus($data['syllabus']);

        return $dto;
    }

    /**
     * @return int
     */
    public function getSemester(): int
    {
        return $this->semester;
    }

    /**
     * @param int $semester
     */
    public function setSemester(int $semester): void
    {
        $this->semester = $semester;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->start_date;
    }

    /**
     * @param string $start_date
     */
    public function setStartDate(string $start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->end_date;
    }

    /**
     * @param string $end_date
     */
    public function setEndDate(string $end_date): void
    {
        $this->end_date = $end_date;
    }

    /**
     * @return Syllabus
     */
    public function getSyllabus(): Syllabus
    {
        return $this->syllabus;
    }

    /**
     * @param Syllabus $syllabus
     */
    public function setSyllabus(Syllabus $syllabus): void
    {
        $this->syllabus = $syllabus;
    }
}
