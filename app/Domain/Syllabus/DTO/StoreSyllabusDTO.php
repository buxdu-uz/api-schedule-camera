<?php

namespace App\Domain\Syllabus\DTO;

class StoreSyllabusDTO
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
     * @param array $data
     * @return StoreSyllabusDTO
     */
    public static function fromArray(array $data): StoreSyllabusDTO
    {
        $dto = new self();
        $dto->setSemester($data['semester']);
        $dto->setStartDate($data['start_date']);
        $dto->setEndDate($data['end_date']);

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
}
