<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PromocodeRequest extends FormRequest
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
            'promocode' => 'required|string|max:255|unique:promocodes',
            'amount' => 'required|integer',
            'use_count' => 'nullable|integer',
            'use_max' => 'nullable|integer',
            'valid_till' => 'nullable|date',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response([
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
