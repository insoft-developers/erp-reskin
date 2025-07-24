<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountProfileRequest extends FormRequest
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
            'old_password' => 'nullable|string',
            'password' => 'nullable|confirmed|min:8',
            'pin' => 'nullable|numeric',
            'profile_picture' => 'nullable',
            'fullname' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Password konfirmasi tidak sama dengan password',
        ];
    }
}
