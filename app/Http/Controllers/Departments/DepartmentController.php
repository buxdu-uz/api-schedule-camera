<?php

namespace App\Http\Controllers\Departments;

use App\Domain\Departments\Repositories\DepartmentRepository;
use App\Domain\Departments\Resources\DepartmentResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * @var mixed|DepartmentRepository
     */
    public mixed $departments;

    /**
     * @param DepartmentRepository $departmentRepository
     */
    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departments = $departmentRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return $this->successResponse('',DepartmentResource::collection($this->departments->getAll()));
    }
}
