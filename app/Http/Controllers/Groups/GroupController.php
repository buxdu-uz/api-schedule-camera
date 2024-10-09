<?php

namespace App\Http\Controllers\Groups;


use App\Domain\Groups\Repositories\GroupRepository;
use App\Domain\Groups\Resources\GroupResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * @var mixed|GroupRepository
     */
    public mixed $groups;

    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groups = $groupRepository;
    }

    public function index(Request $request)
    {
        $request->validate([
            'department_id' => 'required'
        ]);

        return $this->successResponse('', GroupResource::collection($this->groups->getAllGroupDepartmentId($request->department_id)));
    }
}
