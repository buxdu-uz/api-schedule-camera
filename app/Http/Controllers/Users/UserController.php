<?php

namespace App\Http\Controllers\Users;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Users\Repositories\UserRepository;
use App\Domain\Users\Requests\UserFilterRequest;
use App\Domain\Users\Resources\TeacherResource;
use App\Domain\Users\Resources\UserResource;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
     * @return AnonymousResourceCollection
     * @throws BindingResolutionException
     */
    public function paginate(UserFilterRequest $request)
    {
        $filter = app()->make(UserFilter::class, ['queryParams' => array_filter($request->validated())]);
        return UserResource::collection($this->users->paginate($filter,\request()->query('paginate', 20)));
    }

    /**
     * @return JsonResponse
     */
    public function getAllUser()
    {
        return $this->successResponse('',TeacherResource::collection($this->users->getAllTeacher(\request()->query('role'))));
    }

    public function setUserCamera(Request $request)
    {
        $request->validate([
            'user_ids' => 'array|required',
            'camera_ids' => 'array|required',
        ]);
        try {
            for ($i=0; $i<count($request->user_ids); $i++){
                $user = User::query()->find($request->user_ids[$i]);
                $user->cameras()->sync($request->camera_ids[$i]);
            }
            return $this->successResponse('Cameras were attached to the users');
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
