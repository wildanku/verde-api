<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class StartEndDateRequest extends FormRequest
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
            'start_at'  => 'sometimes|nullable|date',
            'end_at'    => 'sometimes|nullable|date|after:start_at'
        ];
    }
}
