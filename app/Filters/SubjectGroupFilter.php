<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class SubjectGroupFilter extends AbstractFilter
{
    public const STATUS = 'status';

    public const GROUP_ID = 'group_id';

    /**
     * @return array[]
     */
    #[ArrayShape([self::STATUS => "array", self::GROUP_ID => "array"])] protected function getCallbacks(): array
    {
        return [
            self::STATUS => [$this, 'status'],
            self::GROUP_ID => [$this, 'group_id'],
        ];
    }

    public function status(Builder $builder, $value): void
    {
        if ($value == -1) {
            $builder->where('status', '=', false);
        } else {
            $builder->where('status', $value);
        }
    }

    public function group_id(Builder $builder, $value): void
    {
        $builder->whereHas('groups', function ($query) use ($value) {
            $query->where('group_id', $value);
        });
    }
}
