<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
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
            'username' => 'required|string|unique:ml_accounts,username',
            'email' => 'required|email|unique:ml_accounts,email',
            'password' => 'required|min:6',
            'branch_id' => 'required|exists:branches,id',
            'position_id' => 'required',
            'phone' => 'required|numeric|min:11',
            'start_date' => 'required|date',
            'pin' => 'required|numeric',
            'is_active' => 'required'
        ];
    }

    public function messages() : array
    {
        return [
            'fullname.required' => 'Nama Staff tidak boleh kosong',
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'branch_id.required' => 'Cabang tidak boleh kosong',
            'position_id.required' => 'Posisi tidak boleh kosong',
            'phone.required' => 'Nomor telepon tidak boleh kosong',
            'phone.numeric' => 'Nomor telepon harus berupa angka',
            'phone.min' => 'Nomor telepon minimal 11 digit',
            'start_date.required' => 'Tanggal mulai bekerja tidak boleh kosong',
            'pin.required' => 'Pin tidak boleh kosong',
            'is_active' => 'Status tidak boleh kosong',
            'email.unique' => 'E-mail sudah digunakan',
        ];
    }
}
