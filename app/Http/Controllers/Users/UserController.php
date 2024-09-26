<?php

namespace App\Http\Controllers\Users;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Users\Repositories\UserRepository;
use App\Domain\Users\Requests\UserFilterRequest;
use App\Domain\Users\Resources\RoleResource;
use App\Domain\Users\Resources\UserRoleResource;
use App\Domain\Users\Resources\UserResource;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

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
        return $this->successResponse('',UserRoleResource::collection($this->users->getAllUser(\request()->query('role'))));
    }

    public function getAllRoles()
    {
        return $this->successResponse('',RoleResource::collection(Role::query()->get()));
    }

    public function setUserCamera(Request $request)
    {
        $request->validate([
            'users' => 'required'
        ]);
        try {
            foreach ($request->users as $role_id => $camera_ids) {
                // Fetch users with the specified role in one query
                $users = User::role($role_id)->get();
                foreach ($users as $user) {
                    // Sync cameras for each user with the role
                    $user->cameras()->sync($camera_ids);
                }
            }
            return $this->successResponse('Cameras were attached to the users');
        }catch (Exception $exception){
            return $this->errorResponse($exception->getMessage());
        }

//        $request->validate([
//            'user_ids' => 'array|required',
//            'camera_ids' => 'array|required',
//        ]);
//        try {
//            for ($i=0; $i<count($request->user_ids); $i++){
//                $user = User::query()->find($request->user_ids[$i]);
//                $user->cameras()->sync($request->camera_ids[$i]);
//            }
//            return $this->successResponse('Cameras were attached to the users');
//        } catch (Exception $exception) {
//            return $this->errorResponse($exception->getMessage());
//        }

    }
}
