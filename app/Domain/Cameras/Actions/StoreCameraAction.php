<?php

namespace App\Domain\Cameras\Actions;

use App\Domain\Cameras\DTO\StoreCameraDTO;
use App\Domain\Cameras\Models\Camera;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreCameraAction
{
    /**
     * @param StoreCameraDTO $dto
     * @return array
     * @throws Exception
     */
    public function execute(StoreCameraDTO $dto): array
    {
        $data = array();
        DB::beginTransaction();
        try {
            foreach ($dto->getCameras() as $cam) {
                $camera = new Camera();
                $camera->name = $cam['name'];
                $camera->link = $cam['link'];
                $camera->save();
//                $camera->syncRoles('camera');
                $data[] = $camera;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $data;
    }
}
