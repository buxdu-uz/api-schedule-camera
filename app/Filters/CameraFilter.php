<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class CameraFilter extends AbstractFilter
{
    public const NAME = 'name';

    public const LINK = 'link';

    /**
     * @return array[]
     */
    #[ArrayShape([self::NAME => "array", self::LINK => "array"])] protected function getCallbacks(): array
    {
        return [
            self::NAME => [$this, 'name'],
            self::LINK => [$this, 'link']
        ];
    }

    public function name(Builder $builder, $value): void
    {
        $builder->where('name', 'like', '%' . $value . '%');
    }

    public function link(Builder $builder, $value): void
    {
        $builder->where('link', 'like', '%' . $value . '%');
    }
}
