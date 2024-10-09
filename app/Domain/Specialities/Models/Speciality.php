<?php

namespace App\Domain\Specialities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;

    protected $fillable=[
        'code',
        'name',
        'department_id',
        'h_structure_type',
        'h_education_type',
        'bachelor_specialty',
        'master_specialty',
        'doctorate_specialty',
        'ordinature_specialty',
    ];
}
