<?php

namespace App\Domain\Cameras\Models;
use App\Domain\Rooms\Models\Room;
use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Camera filter(\App\Filters\FilterInterface $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|Camera newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Camera newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Camera query()
 * @method static \Illuminate\Database\Eloquent\Builder|Camera whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Camera whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Camera whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Camera whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Camera whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Camera extends Model
{
    use Filterable;
    protected $fillable = ['name','link'];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class,'room_camera','camera_id','room_id');
    }
}
