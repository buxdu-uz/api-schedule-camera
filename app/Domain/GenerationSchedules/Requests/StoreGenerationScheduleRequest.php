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
            'data.*.room_id' => ['required', 'exists:rooms,id'],
            'data.*.start_at' => ['required', 'date_format:H:i'],
            'data.*.end_at' => ['required', 'date_format:H:i', 'after:data.*.start_at'],
            'data.*.pair' => ['required', 'integer'],
            'data.*' => [
                function ($attribute, $value, $fail) {

                    $subject_group_id = data_get($value, 'subject_group_id');
                    $room_id = data_get($value, 'room_id');
                    $date = data_get($value, 'date');
                    $start_at = data_get($value, 'start_at');
                    $end_at = data_get($value, 'end_at');
                    $pair = data_get($value, 'pair');

                    // Adjust for proper time format
                    $start_at_full = $start_at . ':00';
                    $end_at_full = $end_at . ':00';

                    // Check for unique combination
                    $exists = GenerationSchedule::query()
                        ->where('subject_group_id', $subject_group_id)
                        ->where('room_id', $room_id)
                        ->where('date', $date)
                        ->where('start_at', $start_at_full)
                        ->where('end_at', $end_at_full)
                        ->where('pair', $pair)
                        ->exists();
                    if ($exists) {
                        $fail("The combination of room, start time, end time, and pair already exists for room ID {$room_id}.");
                    }
                }
            ],
        ];
    }
}
