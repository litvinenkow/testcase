<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4', // |confirmed
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = response([
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }

    public function failRegister()
    {
        $this->validator->errors()
            ->add('other', 'An error has occurred. Please try again');

        $this->failedValidation($this->validator);
    }
}
