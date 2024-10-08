<?php

namespace App\Domain\SubjectGroups\DTO;

class StoreSubjectGroupDTO
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @param array $data
     * @return StoreSubjectGroupDTO
     */
    public static function fromArray(array $data): StoreSubjectGroupDTO
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
