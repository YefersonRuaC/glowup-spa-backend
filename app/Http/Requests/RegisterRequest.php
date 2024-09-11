<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)]
        ];
    }

    public function messages()
    {
        return [
            'name' => 'User name is required',
            'email.required' => 'Email addres is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This user already exists',
            'password' => 'Password must have at least 8 characters'
        ];
    }
}
