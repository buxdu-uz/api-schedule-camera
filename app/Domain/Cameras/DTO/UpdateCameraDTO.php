<?php

namespace App\Domain\Cameras\DTO;

use App\Domain\Cameras\Models\Camera;

class UpdateCameraDTO
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $link;

    /**
     * @var Camera
     */
    private Camera $camera;

    /**
     * @param array $data
     * @return UpdateCameraDTO
     */
    public static function fromArray(array $data): UpdateCameraDTO
    {
        $dto = new self();
        $dto->setName($data['name']);
        $dto->setLink($data['link']);
        $dto->setCamera($data['camera']);

        return $dto;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return Camera
     */
    public function getCamera(): Camera
    {
        return $this->camera;
    }

    /**
     * @param Camera $camera
     */
    public function setCamera(Camera $camera): void
    {
        $this->camera = $camera;
    }
}
