<?php

namespace App\Domain\Favourites\Actions;

use App\Domain\Favourites\DTO\StoreFavouriteDTO;
use App\Domain\Favourites\Models\Favourite;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreFavouriteAction
{
    /**
     * @param StoreFavouriteDTO $dto
     * @return Favourite
     * @throws Exception
     */
    public function execute(StoreFavouriteDTO $dto): Favourite
    {
        DB::beginTransaction();
        try {
            $favourite = new Favourite();
            $favourite->name = $dto->getName();
            $favourite->icon = $dto->getIcon();
            $favourite->save();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $favourite;
    }
}
