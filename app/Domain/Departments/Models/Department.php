<?php

namespace App\Domain\Departments\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;


class Department extends Model
{
    use Filterable;

    protected $fillable = [
        'parent_id',
        'name',
        'code',
        'h_structure_type',
        'h_locality_type',
    ];

    public function parent()
    {
        return $this->BelongsTo(self::class, 'parent_id', 'id');
    }

    public function childrens()
    {
        return $this->HasMany(self::class, 'parent_id', 'id');
    }

    public static function getIdByCode(string $code)
    {
        return self::whereCode($code)->value('id');
    }
}
