<?php

namespace App\Domain\GenerationSchedules\Requests;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Domain\Syllabus\Models\Syllabus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
            'data.*.subject_group_id' => [
                'required',
                'exists:subject_groups,id',
            ],
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
                    $subjectGroupId = data_get($value, 'subject_group_id');

                    // Count existing lectures for the given subject group and date
                    $lectureCount = GenerationSchedule::query()
                        ->whereHas('subjectGroup', function ($query) {
                            $query->where('lesson', '=', 'lecture');
                        })
                        ->where('date', $date)
                        ->where('subject_group_id', $subjectGroupId)
                        ->count();

                    // Include current data array row in the check
                    $lectureInputCount = collect(request('data'))
                        ->where('subject_group_id', $subjectGroupId)
                        ->where('date', $date)
                        ->count();

                    // If lectures exceed one for the given subject group and date, fail validation
                    if (($lectureCount + $lectureInputCount) > 1) {
                        $fail("Only one 'lecture' lesson can be scheduled per day for subject group ID {$subjectGroupId}.");
                        return;
                    }

                    // General uniqueness check for date and pair
                    $pair = data_get($value, 'pair');
                    $exists = GenerationSchedule::query()
                        ->where('subject_group_id', $subjectGroupId)
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
