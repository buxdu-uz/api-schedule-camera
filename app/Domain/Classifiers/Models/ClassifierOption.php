<?php

namespace App\Domain\Classifiers\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassifierOption extends Model
{
    public $timestamps=false;

    protected $fillable=[
        'code',
        'name',
        'classifier_id',
    ];

    /**
     * @param string $string
     * @param \stdClass $subjectGroup
     * @return int
     */
    public static function addNew(string $string, \stdClass $subjectGroup):int
    {
        $key='h_'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
        $classifier=Classifier::updateOrCreate([
            'classifier'=>$key,
        ],[
            'name'=>$key,
            'version'=>1
        ]);
        $option=$classifier->options()->updateOrCreate([
            'code'=>$subjectGroup->code,
        ],[
            'name'=>$subjectGroup->name,
        ]);
        return $option->id;
    }

    public function classifier(): BelongsTo
    {
        return $this->BelongsTo(Classifier::class);
    }

    /**
     * @param string $key
     * @param string $code
     * @return int|null
     */
    public static function getId(string $key, string $code): int|null
    {
        $key=strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
        $key='h_'.$key;
        dd($key,$code);
        try {
            return ClassifierOption::whereHas('classifier', function ($query) use ($key) {
                $query->where('classifier', $key);
            })
                ->where(function ($q) use ($code) {
                    $q->where('code', $code)
                        ->orWhere('name', $code);
                })
                ->firstOrFail()->id;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
