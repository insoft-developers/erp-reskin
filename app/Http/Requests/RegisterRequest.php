<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:ml_accounts,email'],
			'fullname' => ['required', 'min:4', 'max:34'],
			'username' => ['required', 'unique:ml_accounts,username'],
			'whatsapp' => ['required', 'unique:ml_accounts,phone'],
			'password' => ['required', 'min:6', 'confirmed'],
			'tos' => ['required'],
			'category' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.unique' => 'Alamat Email sudah digunakan.',
            'fullname.required' => 'Nama lengkap wajib diisi.',
            'fullname.min' => 'Nama lengkap harus terdiri dari minimal 4 karakter.',
            'fullname.max' => 'Nama lengkap tidak boleh lebih dari 34 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Nama pengguna / Username sudah digunakan.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.unique' => 'Nomor Telepon / WhatsApp sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi harus terdiri dari minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'tos.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            'category.required' => 'Kolom kategori bisnis wajib diisi.',
        ];
    }
}
