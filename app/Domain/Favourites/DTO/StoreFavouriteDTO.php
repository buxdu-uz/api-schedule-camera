<?php

namespace App\Domain\Favourites\DTO;

class StoreFavouriteDTO
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $icon;

    /**
     * @param array $data
     * @return StoreFavouriteDTO
     */
    public static function fromArray(array $data): StoreFavouriteDTO
    {
        $dto = new self();
        $dto->setName($data['name']);
        $dto->setIcon($data['icon']);
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
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }
}
