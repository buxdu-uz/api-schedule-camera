<?php

namespace App\Domain\Favourites\Repositories;

use App\Domain\Favourites\Models\Favourite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FavouriteRepository
{
    /**
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return Favourite::query()
            ->orderBy('name', 'asc')  // 'asc' for ascending or 'desc' for descending
            ->paginate();
    }

    /**
     * @return Builder[]|Collection
     */
    public function getAll(): Collection|array
    {
        return Favourite::query()
            ->get()
            ->sortBy('name');
    }
}
