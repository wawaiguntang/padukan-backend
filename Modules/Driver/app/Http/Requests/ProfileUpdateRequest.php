<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Profile Update Request Validation
 */
class ProfileUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'avatar_file' => 'nullable|file|mimes:jpeg,jpg,png|max:5120', // 5MB max
            'gender' => 'nullable|in:male,female,other',
            'language' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.string' => __('driver::validation.first_name.string'),
            'first_name.max' => __('driver::validation.first_name.max'),
            'last_name.string' => __('driver::validation.last_name.string'),
            'last_name.max' => __('driver::validation.last_name.max'),
            'avatar_file.file' => __('driver::validation.avatar.file'),
            'avatar_file.mimes' => __('driver::validation.avatar.mimes'),
            'avatar_file.max' => __('driver::validation.avatar.max'),
            'gender.in' => __('driver::validation.gender.in'),
            'language.string' => __('driver::validation.language.string'),
            'language.max' => __('driver::validation.language.max'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'status' => false,
            'message' => __('driver::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
