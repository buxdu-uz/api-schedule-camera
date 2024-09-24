<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class UserFilter extends AbstractFilter
{
    public const NAME = 'name';

    public const EMPLOYEE_ID = 'employee_id';

    public const DEPARTMENT_ID = 'department_id';

    /**
     * @return array[]
     */
    #[ArrayShape([self::NAME => "array", self::EMPLOYEE_ID => "array", self::DEPARTMENT_ID => "array"])] protected function getCallbacks(): array
    {
        return [
            self::NAME => [$this, 'name'],
            self::EMPLOYEE_ID => [$this, 'employee_id'],
            self::DEPARTMENT_ID => [$this, 'department_id'],
        ];
    }

    public function name(Builder $builder, $value): void
    {
        $builder->where('name','like','%'.$value.'%');
    }

    public function employee_id(Builder $builder, $value): void
    {
        $builder->where('employee_id',$value);
    }

    public function department_id(Builder $builder, $value): void
    {
        $builder->whereHas('profile', function ($query) use ($value) {
            $query->where('department_id',$value);
        });
    }

}
