<?php

namespace App\Http\Controllers\Users;

use App\Domain\Users\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @var mixed|UserRepository
     */
    public mixed $users;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->users = $userRepository;
    }

    /**
     * @return JsonResponse
     */
    public function paginate()
    {
        return $this->successResponse('', $this->users->paginate(\request()->query('paginate', 20)));
    }
}
