<?php

namespace App\Domain\GenerationSchedules\DTO;

class StoreGenerationScheduleDTO
{
    private array $data;

    /**
     * @param array $data
     * @return StoreGenerationScheduleDTO
     */
    public static function fromArray(array $data): StoreGenerationScheduleDTO
    {
        $dto = new self();
        $dto->setData($data['data']);

        return $dto;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
