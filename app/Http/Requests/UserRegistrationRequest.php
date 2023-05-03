<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationRequest extends FormRequest
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
            'name'                  => 'required|string|min:3|max:100',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'required|min:6|max:20',
            'birth_date'            => 'required|date|before:now',
            'password'              => 'required|string|min:6|max:55|confirmed',
            'password_confirmation' => 'required|string|min:6|max:55'
        ];
    }
}
