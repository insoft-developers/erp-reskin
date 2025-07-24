<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReceivableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'receivable_from' => ['required'],
            'save_to' => ['required', 'exists:ml_current_assets,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Piutang Jangka Panjang', 'Piutang Jangka Pendek'])],
            'sub_type' => ['required'],
            'amount' => ['required'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
