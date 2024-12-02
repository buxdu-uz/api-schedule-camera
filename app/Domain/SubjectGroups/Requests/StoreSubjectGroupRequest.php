<?php

namespace App\Domain\SubjectGroups\Requests;

use App\Domain\SubjectGroups\Models\SubjectGroup;
use App\Enums\FlowOrSplitGroup;
use App\Enums\LessonType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSubjectGroupRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
//        return [
//            'data' => [
//                'required',
//                'array', // Ensure 'data' is an array
//            ],
//            'data.subject_id.*' => ['required','integer'],
//            'data.lesson.*' => ['required', new Enum(LessonType::class)],
//            'data.flow.*' => ['required', new Enum(FlowOrSplitGroup::class)],
//            'data.split_group.*' => ['required', new Enum(FlowOrSplitGroup::class)],
//            'data.lesson_hour.*' => ['required', 'integer'],
//            'data.h_education_year.*' => ['required', 'integer'],
//            'data.semester.*' => ['required', 'integer'],
//        ];

        return [
            'data' => ['required', 'array'],
            'data.*.subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'data.*.lesson' => ['required', new Enum(LessonType::class)],
            'data.*.flow' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.*.split_group' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.*.lesson_hour' => ['required', 'integer', 'min:1'],
            'data.*.education_year' => ['required', 'integer'],
            'data.*.semester' => ['required', 'exists:syllabi,id'],
            'data.*.groups' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1]; // Current index in `data`
                    $flow = request()->input("data.{$index}.flow");

                    if ($flow === 'no' && count($value) > 1) {
                        $fail("Only one group can be added when flow is 'no' (row {$index}).");
                    }
                },
            ],
            'data.*.groups.*' => [
                'integer',
                'exists:groups,id',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1]; // Current index in `data`
                    $subjectId = request()->input("data.{$index}.subject_id");

                    $exists = SubjectGroup::query()
                        ->where('subject_id', $subjectId)
                        ->whereHas('groups', function ($query) use ($value) {
                            $query->where('group_id', $value);
                        })
                        ->exists();

                    if ($exists) {
                        $fail("Group ID {$value} is already attached to subject ID {$subjectId} (row {$index}).");
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'data.subject_id.*.required' => 'Subject ID is required.',
            'data.lesson.*.required' => 'Lesson type is required.',
            'data.flow.*.required' => 'Flow type is required.',
            'data.split_group.*.required' => 'Split group type is required.',
            'data.lesson_hour.*.required' => 'Lesson hour is required.',
            'data.education_year.*.required' => 'Education year is required.',
            'data.semester.*.required' => 'Semester is required.',
            // Add any additional custom messages here
        ];
    }
}
