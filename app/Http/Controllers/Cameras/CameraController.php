<?php

namespace App\Http\Controllers\Cameras;

use App\Domain\Cameras\Actions\StoreCameraAction;
use App\Domain\Cameras\Actions\UpdateCameraAction;
use App\Domain\Cameras\DTO\StoreCameraDTO;
use App\Domain\Cameras\DTO\UpdateCameraDTO;
use App\Domain\Cameras\Models\Camera;
use App\Domain\Cameras\Repositories\CameraRepository;
use App\Domain\Cameras\Requests\CameraFilterRequest;
use App\Domain\Cameras\Requests\StoreCameraRequest;
use App\Domain\Cameras\Requests\UpdateCameraRequest;
use App\Domain\Cameras\Resources\CameraResource;
use App\Domain\Users\Requests\UserFilterRequest;
use App\Filters\CameraFilter;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use Exception;

class CameraController extends Controller
{
    /**
     * @var mixed|CameraRepository
     */
    public mixed $cameras;

    /**
     * @param CameraRepository $cameraRepository
     */
    public function __construct(CameraRepository $cameraRepository)
    {
        $this->cameras = $cameraRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CameraFilterRequest $request)
    {
        $filter = app()->make(CameraFilter::class, ['queryParams' => array_filter($request->validated())]);
        return CameraResource::collection($this->cameras->paginate($filter, \request()->query('paginate', 20)));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCameraRequest $request, StoreCameraAction $action)
    {
        try {
            $dto = StoreCameraDTO::fromArray($request->validated());
            $response = $action->execute($dto);
            return $this->successResponse('Cameras created successfully.', CameraResource::collection($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCameraRequest $request, Camera $camera, UpdateCameraAction $action)
    {
        try {
            $dto = UpdateCameraDTO::fromArray(array_merge($request->validated(), ['camera' => $camera]));
            $response = $action->execute($dto);
            return $this->successResponse('Cameras updated successfully.', new CameraResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Camera $camera)
    {
        $camera->delete();

        return $this->successResponse('Camera deleted successfully.');
    }
}
