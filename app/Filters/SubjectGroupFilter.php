<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class SubjectGroupFilter extends AbstractFilter
{
    public const STATUS = 'status';

    /**
     * @return array[]
     */
    #[ArrayShape([self::STATUS => "array"])] protected function getCallbacks(): array
    {
        return [
            self::STATUS => [$this, 'status']
        ];
    }

    public function status(Builder $builder, $value): void
    {
        if ($value == -1){
            $builder->where('status', '=',false);
        }else{
            $builder->where('status', $value);
        }
    }
}
