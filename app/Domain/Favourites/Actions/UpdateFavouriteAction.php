<?php

namespace App\Domain\Favourites\Actions;

use App\Domain\Favourites\DTO\StoreFavouriteDTO;
use App\Domain\Favourites\DTO\UpdateFavouriteDTO;
use App\Domain\Favourites\Models\Favourite;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateFavouriteAction
{
    /**
     * @param UpdateFavouriteDTO $dto
     * @return Favourite
     * @throws Exception
     */
    public function execute(UpdateFavouriteDTO $dto): Favourite
    {
        DB::beginTransaction();
        try {
            $favourite = $dto->getFavourite();
            $favourite->name = $dto->getName();
            $favourite->icon = $dto->getIcon();
            $favourite->update();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $favourite;
    }
}
