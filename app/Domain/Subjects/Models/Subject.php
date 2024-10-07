<?php

namespace App\Domain\Subjects\Models;

use App\Domain\Classifiers\Models\ClassifierOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'active',
        'h_subject_block',
        'h_education_type',
    ];

    public function subjectBlock(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_subject_block','id');
    }

    public function educationType(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_education_type','id');
    }
}
