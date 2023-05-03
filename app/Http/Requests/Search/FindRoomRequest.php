<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class FindRoomRequest extends FormRequest
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
            'name'      => 'sometimes|nullable|min:1|max:55',
            'theme'     => 'sometimes|nullable|min:1|max:55',
            'pax'       => 'sometimes|nullable|numeric|min:1|max:99',
            'checkin'   => 'sometimes|nullable|date|after_or_equal:today',
            'checkout'  => 'sometimes|nullable|date|after:checkin',
            'offset'    => 'sometimes|nullable|min:1|max:100',
            'page'      => 'sometimes|nullable|min:1'
        ];
    }
}
