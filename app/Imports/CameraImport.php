<?php

namespace App\Imports;

use App\Domain\Cameras\Models\Camera;
use App\Domain\Floors\Models\Floor;
use App\Domain\Rooms\Models\Room;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CameraImport implements ToModel,WithHeadingRow,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $camera = new Camera();
        $camera->name = $row['camera_name'];
        $camera->link = $row['camera_link'];
        $camera->save();

        Floor::updateOrCreate([
            'name' => $row['floor'],
        ], [
            'name' => $row['floor'],
        ]);

        $room = Room::query()->find($row['room_id']);
        $room->cameras()->attach($camera->id);
        return $camera;
    }

    public function rules(): array
    {
        return [
            'camera_name' => 'required|unique:cameras,name',
            'camera_link' => 'required|unique:cameras,link',
            'floor' => 'required',
            'room_id' => 'required'
        ];
    }
}
