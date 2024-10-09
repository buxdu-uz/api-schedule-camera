<?php

namespace App\Domain\SubjectGroups\Models;

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Subjects\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectGroup extends Model
{
    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class,'teacher_id','id');
    }

    public function educationYear(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_education_year','id');
    }
}
