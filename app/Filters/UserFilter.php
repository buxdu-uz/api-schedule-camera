<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class UserFilter extends AbstractFilter
{
    public const REQUEST_ID = 'request_id';

    /**
     * @return array[]
     */
    #[ArrayShape([self::REQUEST_ID => "array"])] protected function getCallbacks(): array
    {
        return [
            self::REQUEST_ID => [$this, 'request_id']
        ];
    }

    public function con_subject_id(Builder $builder, $value): void
    {
        $builder->whereRaw('JSON_CONTAINS(consumptions->"$[*].conSubject.id", "' . $value . '")');
    }

}
