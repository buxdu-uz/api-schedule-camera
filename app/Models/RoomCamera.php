<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomCamera extends Model
{
    use HasFactory;

    protected $fillable = ['room_id','camera_id'];

    protected $table = 'room_camera';
}
