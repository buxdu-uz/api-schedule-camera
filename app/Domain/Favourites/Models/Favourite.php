<?php

namespace App\Domain\Favourites\Models;

use App\Domain\Cameras\Models\Camera;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Favourite extends Model
{
    protected $perPage = 8;

    public function cameras(): BelongsToMany
    {
        return $this->belongsToMany(Camera::class,'favourite_cameras','favourite_id','camera_id');
    }
}
