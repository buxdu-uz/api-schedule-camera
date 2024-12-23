<?php

namespace App\Domain\GenerationSchedules\Requests;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
                    $subjectGroup = SubjectGroup::find($value);
                    if (!$subjectGroup || !$subjectGroup->syllabi) {
                        $fail("Subject group ID {$value} uchun syllabus mavjud emas.");
                        return;
                    }

                    $syllabusStartDate = Carbon::parse($subjectGroup->syllabi->start_date);
                    $syllabusEndDate = Carbon::parse($subjectGroup->syllabi->end_date);

                    preg_match('/data\.(\d+)\.subject_group_id/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    if ($index === null) {
                        $fail("Invalid attribute structure.");
                        return;
                    }

                    $date = request("data.$index.date");
                    if (!$date || Carbon::parse($date)->lt($syllabusStartDate) || Carbon::parse($date)->gt($syllabusEndDate)) {
                        $fail("Berilgan sana syllabus start_date va end_date orasida emas.");
                    }
                },
            ],
            'data.*.date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()], // Prevent past dates
            'data.*.pair' => ['required', 'integer'],
            'data.*' => [
                function ($attribute, $value, $fail) {
                    $date = data_get($value, 'date');
                    $subjectGroupId = data_get($value, 'subject_group_id');
                    $pair = data_get($value, 'pair');

                    $subjectGroup = SubjectGroup::find($subjectGroupId);
                    if (!$subjectGroup) {
                        $fail("Subject group ID {$subjectGroupId} mavjud emas.");
                        return;
                    }

                    $syllabusStartDate = Carbon::parse($date);
                    $syllabusEndDate = Carbon::parse($subjectGroup->syllabi->end_date);

                    $weeksCount = $syllabusStartDate->diffInWeeks($syllabusEndDate);
                    $totalPairs = $subjectGroup->lesson_hour / 2;

                    // ** Yangi qo'shilgan shart: Agar totalPairs GenerationSchedule dagi mavjud rekordlar soniga teng bo'lsa **
                    $existingScheduleCount = GenerationSchedule::query()
                        ->where('subject_group_id', $subjectGroupId)
                        ->where('teacher_id', Auth::id())
                        ->count();

                    if ($existingScheduleCount >= $totalPairs) {
                        $fail("Subject group ID {$subjectGroupId} uchun barcha soatlar biriktirilgan.");
                        return;
                    }


                    if ($weeksCount > 0) {
                        $pairsPerWeek = ceil($totalPairs / $weeksCount);
                        $weeklyPairsCount = GenerationSchedule::query()
                            ->where('subject_group_id', $subjectGroupId)
                            ->whereBetween('date', [$syllabusStartDate, $syllabusEndDate])
                            ->whereRaw('WEEK(date) = WEEK(?)', [$date])
                            ->count();

                        if ($weeklyPairsCount >= $pairsPerWeek) {
                            $fail("Subject group ID {$subjectGroupId} haftada faqat {$pairsPerWeek} marta dars bo'lishi mumkin.");
                        }
                    } else {
                        $existingScheduleCount = GenerationSchedule::query()
                            ->where('subject_group_id', $subjectGroupId)
                            ->whereBetween('date', [$syllabusStartDate, $syllabusEndDate])
                            ->count();

                        if ($existingScheduleCount >= $totalPairs) {
                            $fail("Subject group ID {$subjectGroupId} uchun faqat syllabusdagi sanalargacha bo'lgan darslar biriktirilishi mumkin.");
                        }
                    }

                    if ($subjectGroup->lesson === 'lecture' && Carbon::parse($date)->dayOfWeek > 6) {
                        $lectureCount = GenerationSchedule::query()
                            ->where('subject_group_id', $subjectGroupId)
                            ->where('date', $date)
                            ->count();

                        if ($lectureCount >= 1) {
                            $fail("Subject group ID {$subjectGroupId} uchun bir kunda faqat bitta 'lecture' dars bo'lishi mumkin.");
                        }
                    }

                    if(Carbon::parse($date)->dayOfWeek > 6){
                        $exists = GenerationSchedule::query()
                            ->where('subject_group_id', $subjectGroupId)
                            ->where('date', $date)
                            ->where('pair', $pair)
                            ->exists();

                        if ($exists) {
                            $fail("Subject group ID {$subjectGroupId} uchun sana va juftlik kombinatsiyasi allaqachon mavjud.");
                        }
                        $groupIds = $subjectGroup->groups->pluck('id');

                        $groupConflict = GenerationSchedule::query()
                            ->whereHas('subjectGroup.groups', function ($query) use ($groupIds) {
                                $query->whereIn('group_id', $groupIds);
                            })
                            ->where('date', $date)
                            ->where('pair', $pair)
                            ->exists();

                        if ($groupConflict) {
                            $fail("Ushbu guruhlarga ushbu parada oldin dars biriktirilgan.");
                        }
                    }

                },
            ],
        ];
    }
}
