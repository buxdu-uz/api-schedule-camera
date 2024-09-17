<?php

namespace App\Http\Controllers\Buildings;

use App\Domain\Buildings\Repositories\BuildingRepository;
use App\Domain\Buildings\Resources\BuildingResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BuildingController extends Controller
{
    /**
     * @var mixed|BuildingRepository
     */
    public mixed $buildings;

    /**
     * @param BuildingRepository $buildingRepository
     */
    public function __construct(BuildingRepository $buildingRepository)
    {
        $this->buildings = $buildingRepository;
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        return $this->successResponse('',BuildingResource::collection($this->buildings->getAll()));
    }
}
