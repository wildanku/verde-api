<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
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
            'name'          => 'required|string|min:3|max:55',
            'theme'         => 'sometimes|nullable|min:3|max:55',
            'pax'           => 'required|numeric|min:0|max:999',
            'price'         => 'required|numeric|min:0|max:999999999',
            'description'   => 'sometimes|nullable|min:3|max:65535'
        ];
    }
}
