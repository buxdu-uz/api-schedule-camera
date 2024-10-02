<?php

namespace App\Domain\Cameras\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCameraRequest extends FormRequest
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
            'room_id' => 'required',
            'name' => 'required|string|max:255',
            'link' => 'required',
            'camera' => 'sometimes|json',
            'favourite' => 'sometimes|boolean',
        ];
    }
}
