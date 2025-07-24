<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10'],
            'type' => ['required', 'string', Rule::in('persen', 'nominal')],
            'value' => ['required'],
            'expired_at' => ['required', 'date'],
            'min_order' => ['required'],
            // 'max_use' => ['required', 'numeric'],
        ];
    }
}
