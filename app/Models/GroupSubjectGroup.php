<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSubjectGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['subject_group_id','group_id'];

    protected $table = 'group_subject_group';
}
