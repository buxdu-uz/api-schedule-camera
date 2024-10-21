<?php

namespace App\Domain\Favourites\DTO;

use App\Domain\Favourites\Models\Favourite;

class UpdateFavouriteDTO
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
     * @var Favourite
     */
    private Favourite $favourite;

    /**
     * @param array $data
     * @return UpdateFavouriteDTO
     */
    public static function fromArray(array $data): UpdateFavouriteDTO
    {
        $dto = new self();
        $dto->setName($data['name']);
        $dto->setIcon($data['icon']);
        $dto->setFavourite($data['favourite']);
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

    /**
     * @return Favourite
     */
    public function getFavourite(): Favourite
    {
        return $this->favourite;
    }

    /**
     * @param Favourite $favourite
     */
    public function setFavourite(Favourite $favourite): void
    {
        $this->favourite = $favourite;
    }
}
