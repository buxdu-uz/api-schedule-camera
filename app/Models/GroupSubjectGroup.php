<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSubjectGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['subject_group_id','group_id'];

    protected $table = 'group_subject_group';

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class,'teacher_id','id');
    }
}
