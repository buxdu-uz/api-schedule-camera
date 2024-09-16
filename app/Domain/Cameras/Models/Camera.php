<?php

namespace App\Domain\Cameras\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Camera extends Authenticatable
{
    use HasApiTokens, HasRoles;
}
