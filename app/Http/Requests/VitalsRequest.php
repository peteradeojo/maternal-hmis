<?php

namespace App\Http\Requests;

use App\Enums\Department;
use Illuminate\Foundation\Http\FormRequest;

class VitalsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->department_id == Department::NUR->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'temperature' => 'nullable|numeric',
            'blood_pressure' => ['nullable', function ($attr, $value, $fail) {
                if (!preg_match('/^\d{2,3}\/\d{2,3}$/', $value)) {
                    $fail('Invalid blood pressure format');
                }
            }],
            'pulse' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'lmp' => 'nullable|date',
            'edd' => 'required_with:lmp|date',
            'gravida'  => 'required|integer',
            'parity'  => 'required|integer',
        ];
    }
}
