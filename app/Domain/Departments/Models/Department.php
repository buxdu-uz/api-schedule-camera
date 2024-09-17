<?php

namespace App\Domain\Departments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Department> $childrens
 * @property-read int|null $childrens_count
 * @property-read Department|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',
        'name',
        'code'
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
        return Department::whereCode($code)->value('id');
    }
}
