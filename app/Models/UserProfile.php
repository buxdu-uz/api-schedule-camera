<?php

namespace App\Models;

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Departments\Models\Department;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $user_id
 * @property int|null $department_id
 * @property string $full_name
 * @property string $short_name
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $third_name
 * @property int|null $year_of_enter
 * @property int|null $h_employee_status
 * @property int|null $h_employee_type
 * @property string|null $birth_date
 * @property string|null $contract_number
 * @property string|null $decree_number
 * @property string|null $passport_series
 * @property int|null $passport_pinfl
 * @property string|null $passport_at
 * @property string|null $passport_where
 * @property string|null $contract_date
 * @property string|null $decree_date
 * @property string|null $tutorGroups
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Department|null $department
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereContractDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereContractNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereDecreeDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereDecreeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereHEmployeeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereHEmployeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePassportAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePassportPinfl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePassportSeries($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile wherePassportWhere($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereThirdName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereTutorGroups($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserProfile whereYearOfEnter($value)
 * @mixin \Eloquent
 */
class UserProfile extends Model
{
    use Filterable;

    protected $guarded=false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->BelongsTo(ClassifierOption::class,'h_staff_position','id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function user_gender(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'gender','id');
    }

    public function academicDegree(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_academic_degree','id');
    }
    public function academicRank(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_academic_rank','id');
    }
    public function employmentForm(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_employment_form','id');
    }
    public function employmentStaff(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_employment_staff','id');
    }
    public function staffPosition(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_staff_position','id');
    }
    public function employeeStatus(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_employee_status','id');
    }
    public function employeeType(): BelongsTo
    {
        return $this->belongsTo(ClassifierOption::class,'h_employee_type','id');
    }
}
