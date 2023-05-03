<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'room_id'   => 'required|exists:rooms,id',
            'pax'       => 'required|numeric|min:1|max:99',
            'checkin'   => 'required|date|after_or_equal:today',
            'checkout'  => 'required|date|after:checkin',
            'notes'     => 'sometimes|min:3|max:255'
        ];
    }
}
