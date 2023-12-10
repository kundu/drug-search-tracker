<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDrugRequest extends FormRequest
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
            'rxcui' => 'required|string', // Add additional validation as necessary
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'rxcui.required' => 'The RXCUI is required for adding a drug.',
        ];
    }
}
