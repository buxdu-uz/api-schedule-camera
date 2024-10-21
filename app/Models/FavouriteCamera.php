<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteCamera extends Model
{
    use HasFactory;

    protected $fillable = ['favourite_id','camera_id'];
}
