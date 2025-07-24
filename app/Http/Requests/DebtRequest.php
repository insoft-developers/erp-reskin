<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DebtRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required'],
            'debt_from' => ['required'],
            'save_to' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Utang Jangka Panjang', 'Utang Jangka Pendek'])],
            'sub_type' => ['required'],
            'amount' => ['required'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
