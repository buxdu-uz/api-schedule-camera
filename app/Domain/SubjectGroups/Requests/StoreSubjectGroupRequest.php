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
        return [
            'data' => [
                'required',
                'array', // Ensure 'data' is an array
            ],
            'data.subject_id.*' => ['required','integer'],
            'data.lesson.*' => ['required', new Enum(LessonType::class)],
            'data.flow.*' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.split_group.*' => ['required', new Enum(FlowOrSplitGroup::class)],
            'data.lesson_hour.*' => ['required', 'integer'],
            'data.h_education_year.*' => ['required', 'integer'],
            'data.semester.*' => ['required', 'integer'],
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
