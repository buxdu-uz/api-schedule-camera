<?php

namespace App\Domain\Cameras\DTO;

class StoreCameraDTO
{
    /**
     * @var array
     */
    private array $cameras;

    /**
     * @param array $data
     * @return StoreCameraDTO
     */
    public static function fromArray(array $data): StoreCameraDTO
    {
        $dto = new self();
        $dto->setCameras($data['cameras']);

        return $dto;
    }

    /**
     * @return array
     */
    public function getCameras(): array
    {
        return $this->cameras;
    }

    /**
     * @param array $cameras
     */
    public function setCameras(array $cameras): void
    {
        $this->cameras = $cameras;
    }
}
