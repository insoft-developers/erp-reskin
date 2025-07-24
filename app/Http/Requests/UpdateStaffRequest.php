<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
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
            'fullname' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|min:6',
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'required',
            'phone' => 'required|numeric',
            'start_date' => 'required|date',
            'pin' => 'nullable|numeric',
            'is_active' => 'required'
        ];
    }
}
