<?php

namespace App\Imports;

use App\Domain\Cameras\Models\Camera;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CameraImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Camera([
            'name' => $row['name'],
            'link' => $row['link'],
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:cameras,name',
            'link' => 'required|unique:cameras,link'
        ];
    }
}
