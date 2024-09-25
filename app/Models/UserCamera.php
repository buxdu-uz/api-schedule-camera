<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCamera extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','camera_id'];

    protected $table = 'user_camera';
}
