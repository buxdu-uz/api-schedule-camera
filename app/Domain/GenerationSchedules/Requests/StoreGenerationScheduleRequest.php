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
        return [
            'data' => ['required', 'array'],
            'data.*.subject_group_id' => [
                'required',
                'exists:subject_groups,id',
                function ($attribute, $value, $fail) {
                    // Request orqali kelayotgan subject_group_id ni syllabus bilan bog‘lash
                    $subjectGroup = SubjectGroup::query()->find($value);
                    if (!$subjectGroup || !$subjectGroup->syllabi) {
                        $fail("Subject group ID {$value} uchun syllabus mavjud emas.");
                        return;
                    }

                    $syllabusStartDate = Carbon::parse($subjectGroup->syllabi->start_date)->toDateString();
                    $syllabusEndDate = Carbon::parse($subjectGroup->syllabi->end_date)->toDateString();
                    // Extract the current index (e.g., data.0.subject_group_id -> 0)
                    preg_match('/data\.(\d+)\.subject_group_id/', $attribute, $matches);
                    $index = $matches[1] ?? null;

                    if ($index === null) {
                        $fail("Invalid attribute structure.");
                        return;
                    }

                    // Get the date for the corresponding item
                    $date = request("data.$index.date");
                    if (!$date || $date < $syllabusStartDate || $date > $syllabusEndDate) {
                        $fail("Berilgan sana syllabusning start_date va end_date orasida emas: {$syllabusStartDate} va {$syllabusEndDate}.");
                    }
                },
            ],
            'data.*.date' => [
                'required',
                'date',
            ],
            'data.*.pair' => ['required', 'integer'],
            'data.*' => [
                function ($attribute, $value, $fail) {
                    $date = data_get($value, 'date');
                    $subjectGroupId = data_get($value, 'subject_group_id');

                    // Tekshiruv: berilgan sana uchun faqat bitta lecture mavjud bo'lishi kerak
                    $lectureCount = GenerationSchedule::query()
                        ->whereHas('subjectGroup', function ($query) {
                            $query->where('lesson', '=', 'lecture');
                        })
                        ->where('date', $date)
                        ->where('subject_group_id', $subjectGroupId)
                        ->count();

                    $lectureInputCount = collect(request('data'))
                        ->where('subject_group_id', $subjectGroupId)
                        ->where('date', $date)
                        ->count();

                    if (($lectureCount + $lectureInputCount) > 1) {
                        $fail("Subject group ID {$subjectGroupId} uchun bir kunda faqat bitta 'lecture' bo'lishi mumkin.");
                        return;
                    }

                    // Sana va juftlik bo‘yicha umumiy unikal bo'lishini tekshirish
                    $pair = data_get($value, 'pair');
                    $exists = GenerationSchedule::query()
                        ->where('subject_group_id', $subjectGroupId)
                        ->where('date', $date)
                        ->where('pair', $pair)
                        ->exists();

                    if ($exists) {
                        $fail("Subject group ID {$subjectGroupId} uchun sana va juftlik kombinatsiyasi allaqachon mavjud.");
                    }
                },
            ],
        ];
    }
}
