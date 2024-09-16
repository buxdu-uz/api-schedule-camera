<?php

namespace App\Domain\Cameras\Models;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Camera extends Model
{
    use Filterable;
}
