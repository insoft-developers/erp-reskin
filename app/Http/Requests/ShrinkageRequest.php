<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShrinkageRequest extends FormRequest
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
            'ml_fixed_asset_id' => ['required', 'exists:ml_fixed_assets,id'],
            'ml_accumulated_depreciation_id' => ['required', 'exists:ml_accumulated_depreciation,id'],
            'ml_admin_general_fee_id' => ['required', 'exists:ml_admin_general_fees,id'],
            'name' => ['required', 'string', 'max:255'],
            'initial_value' => ['required'],
            'useful_life' => ['required', 'numeric'],
            'residual_value' => ['required'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
