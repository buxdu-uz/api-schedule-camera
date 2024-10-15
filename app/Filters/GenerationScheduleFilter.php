<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class GenerationScheduleFilter extends AbstractFilter
{
    public const GROUP_ID = 'group_id';

    /**
     * @return array[]
     */
    #[ArrayShape([self::GROUP_ID => "array"])] protected function getCallbacks(): array
    {
        return [
            self::GROUP_ID => [$this, 'group_id']
        ];
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return void
     */
    public function group_id(Builder $builder, $value): void
    {
        $builder->whereHas('subjectGroup', function ($query) use ($value) {
            $query->whereHas('groups', function ($subQuery) use ($value) {
                $subQuery->where('group_id', $value);
            });
        });
    }
}
