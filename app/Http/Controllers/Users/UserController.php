<?php

namespace App\Http\Controllers\Users;

use App\Domain\Users\Repositories\UserRepository;
use App\Domain\Users\Requests\UserFilterRequest;
use App\Domain\Users\Resources\UserResource;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws BindingResolutionException
     */
    public function paginate(UserFilterRequest $request)
    {
        $filter = app()->make(UserFilter::class, ['queryParams' => array_filter($request->validated())]);
        return UserResource::collection($this->users->paginate($filter,\request()->query('paginate', 20)));
    }
}
