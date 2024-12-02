<?php

namespace App\Domain\SubjectGroups\Models;

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Groups\Models\Group;
use App\Domain\Subjects\Models\Subject;
use App\Domain\Syllabus\Models\Syllabus;
use App\Models\Traits\Filterable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubjectGroup extends Model
{
    use Filterable;

    protected $fillable = ['status'];

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

    public function syllabi(): BelongsTo
    {
        return $this->belongsTo(Syllabus::class,'syllabus_id','id');
    }

    public function educationYear(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_education_year','id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class,'group_subject_group','subject_group_id','group_id');
    }
}
