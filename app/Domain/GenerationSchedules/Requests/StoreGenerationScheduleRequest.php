<?php

namespace App\Domain\GenerationSchedules\Requests;

use App\Domain\GenerationSchedules\Models\GenerationSchedule;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreGenerationScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'data.*.subject_group_id' => [
                'required',
                'exists:subject_groups,id',
                function ($attribute, $value, $fail) {
                    // Subject groupni olish
                    $subjectGroup = SubjectGroup::find($value);
                    if (!$subjectGroup || !$subjectGroup->syllabi) {
                        $fail("Subject group ID {$value} uchun syllabus mavjud emas.");
                        return;
                    }

                    // Syllabusning start_date va end_date oralig'ini aniqlash
                    $syllabusStartDate = Carbon::parse($subjectGroup->syllabi->start_date);
                    $syllabusEndDate = Carbon::parse($subjectGroup->syllabi->end_date);

                    // Sanani olish
                    preg_match('/data\.(\d+)\.subject_group_id/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    $date = request("data.$index.date");

                    if (!$date || $date < $syllabusStartDate->toDateString() || $date > $syllabusEndDate->toDateString()) {
                        $fail("Berilgan sana syllabus start_date va end_date oralig'ida bo'lishi kerak: {$syllabusStartDate->toDateString()} va {$syllabusEndDate->toDateString()}.");
                        return;
                    }

                    // Haftalar sonini hisoblash
                    $weeksCount = $syllabusStartDate->diffInWeeks($syllabusEndDate) + 1;

                    // Bir haftada qo'yiladigan maksimal darslar soni
                    $totalPairs = $subjectGroup->lesson_hour / 2; // 60 soat -> 30 para
                    $maxLessonsPerWeek = (int) ceil($totalPairs / $weeksCount);

                    // Ushbu haftaga darslar sonini tekshirish
                    $weekStart = Carbon::parse($date)->startOfWeek();
                    $weekEnd = Carbon::parse($date)->endOfWeek();

                    $weeklyLessons = GenerationSchedule::query()
                        ->whereBetween('date', [$weekStart, $weekEnd])
                        ->where('subject_group_id', $value)
                        ->count();
                    if ($weeklyLessons >= $maxLessonsPerWeek) {
                        $fail("Subject group ID {$value} uchun haftasiga maksimal {$maxLessonsPerWeek} ta dars qo'yilishi mumkin.");
                    }

                    $dailyLessons = GenerationSchedule::query()
                        ->where('date', $date)
                        ->where('subject_group_id', $value)
                        ->count();

                    if ($subjectGroup && $subjectGroup->lesson === 'lecture' && $dailyLessons > 0) {
                        $fail("Subject group ID {$value} uchun bir kunda faqat bitta lecture dars bo'lishi mumkin.");
                    }
                },
            ],
            'data.*.date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    // Haftaning bir kuniga faqat bitta dars bo'lishini tekshirish
                    $subjectGroupId = data_get(request('data'), str_replace('.date', '.subject_group_id', $attribute));

                    $dailyLessons = GenerationSchedule::query()
                        ->where('date', $value)
                        ->where('subject_group_id', $subjectGroupId)
                        ->count();

                    if ($dailyLessons > 0) {
                        $fail("Sana {$value} uchun faqat bitta dars qo'yilishi mumkin.");
                    }
                },
            ],
            'data.*.pair' => ['required', 'integer'],
        ];
    }
}
