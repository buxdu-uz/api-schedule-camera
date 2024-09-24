<?php

namespace App\Http\Controllers\Buildings;

use App\Domain\Buildings\Repositories\BuildingRepository;
use App\Domain\Buildings\Resources\BuildingResource;
use App\Domain\Rooms\Models\Room;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return $this->successResponse('', BuildingResource::collection($this->buildings->getAll()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setRoomCamera(Request $request)
    {
        $request->validate([
            'room_ids' => 'array|required',
            'camera_ids' => 'array|required',
        ]);
        try {
            for ($i=0; $i<count($request->room_ids); $i++){
                $room = Room::query()->find($request->room_ids[$i]);
                $room->cameras()->sync($request->camera_ids[$i]);
            }
            return $this->successResponse('Cameras were attached to the rooms');
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}
