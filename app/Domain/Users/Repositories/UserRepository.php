<?php

namespace App\Domain\Users\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * @param $paginate
     * @return LengthAwarePaginator
     */
    public function paginate($paginate): LengthAwarePaginator
    {
        return User::query()
            ->orderByDesc('id')
            ->paginate($paginate);
    }
}
