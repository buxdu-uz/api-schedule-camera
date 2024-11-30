<?php

namespace App\Domain\SubjectGroups\Requests;

use App\Enums\FlowOrSplitGroup;
use App\Enums\LessonType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SubjectGroupFilterRequest extends FormRequest
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
            'status' => 'sometimes',
            'group_id' => 'sometimes'
        ];
    }
}
