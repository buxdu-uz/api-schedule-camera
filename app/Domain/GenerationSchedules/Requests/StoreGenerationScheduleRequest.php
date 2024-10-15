<?php

namespace App\Domain\GenerationSchedules\Requests;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\Syllabus\Models\Syllabus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreGenerationScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $syllabus = Syllabus::query()->latest()->first();
        $start_date = $syllabus ? Carbon::parse($syllabus->start_date)->toDateString() : null;
        $end_date = $syllabus ? Carbon::parse($syllabus->end_date)->toDateString() : null;

        return [
            'data' => ['required', 'array'],
            'data.*.subject_group_id' => ['required', 'exists:subject_groups,id'],
            'data.*.date' => [
                'required',
                'date',
                'after_or_equal:' . $start_date,
                'before_or_equal:' . $end_date,
            ],
            'data.*.pair' => ['required', 'integer'],
            'data.*' => [
                function ($attribute, $value, $fail) {
                    $date = data_get($value, 'date');
                    $pair = data_get($value, 'pair');

                    // Check for unique combination
                    $exists = GenerationSchedule::query()
                        ->where('date', $date)
                        ->where('pair', $pair)
                        ->exists();
                    if ($exists) {
                        $fail("The combination of date and pair already exists for date {$date}.");
                    }
                }
            ],
        ];
    }
}
