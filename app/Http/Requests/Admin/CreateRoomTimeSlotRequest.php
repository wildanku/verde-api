<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomTimeSlotRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_at'          => 'required|date|after:today',
            'end_at'            => 'required|date|after:start_at',
            'is_weekend_only'   => 'sometimes|nullable|boolean',
            'is_weekday_only'   => 'sometimes|nullable|boolean',
            'days'              => 'sometimes|nullable|array',
            'days.*'            => 'sometimes|nullable|string|in:sun,mon,tue,wed,thu,fri,sat',
            'price'             => 'sometimes|nullable|integer|min:0|max:9999999999'
        ];
    }
}
