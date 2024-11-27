<?php

namespace App\Domain\SubjectGroups\Requests;

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
            'data' => [
                'required','array'
            ],
            'data.subject_id.*' => ['required', 'integer', 'exists:subjects,id'],
            'data.lesson.*' => ['required', new Enum(LessonType::class)],
            'data.flow.*' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.split_group.*' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.lesson_hour.*' => ['required', 'integer', 'min:1'],
            'data.education_year.*' => ['required', 'integer'],
            'data.semester.*' => ['required', 'integer', 'min:1', 'max:2'],
            'data.group_ids.*' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->flow === 'no' && count($value) > 1) {
                        $fail('Only one group can be added when flow is no.');
                    }
                },
            ],
            'data.group_ids.*.*' => [
                'integer',
                'exists:groups,id',
                function ($attribute, $value, $fail) {
                    $subjectId = $this->input('subject_id');
                    $exists = \DB::table('group_subject_group')
                        ->join('subject_groups', 'group_subject_group.subject_group_id', '=', 'subject_groups.id')
                        ->where('subject_groups.subject_id', $subjectId)
                        ->where('group_subject_group.group_id', $value)
                        ->exists();

                    if ($exists) {
                        $fail("Group ID {$value} is already attached to this subject.");
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
