<?php

namespace App\Domain\Cameras\Actions;

use App\Domain\Cameras\DTO\StoreCameraDTO;
use App\Domain\Cameras\DTO\UpdateCameraDTO;
use App\Domain\Cameras\Models\Camera;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateCameraAction
{
    /**
     * @param UpdateCameraDTO $dto
     * @return Camera
     * @throws Exception
     */
    public function execute(UpdateCameraDTO $dto): Camera
    {
        DB::beginTransaction();
        try {
            $camera = $dto->getCamera();
            $camera->name = $dto->getName();
            $camera->link = $dto->getLink();
            $camera->update();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $camera;
    }
}
