<?php

namespace App\Domain\Classifiers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classifier extends Model
{
    protected $fillable=[
        'name',
        'classifier',
        'version',
    ];

    public function options():HasMany
    {
        return $this->HasMany(ClassifierOption::class);
    }
}
