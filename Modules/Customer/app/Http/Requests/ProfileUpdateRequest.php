<?php

namespace Modules\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Customer\Enums\GenderEnum;

/**
 * Profile Update Request
 *
 * Validates profile update data
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
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
            'first_name.string' => __('customer::validation.first_name.string'),
            'first_name.max' => __('customer::validation.first_name.max'),
            'last_name.string' => __('customer::validation.last_name.string'),
            'last_name.max' => __('customer::validation.last_name.max'),
            'avatar_file.file' => __('customer::validation.avatar_file.file'),
            'avatar_file.mimes' => __('customer::validation.avatar_file.mimes'),
            'avatar_file.max' => __('customer::validation.avatar_file.max'),
            'gender.in' => __('customer::validation.gender.in'),
            'language.string' => __('customer::validation.language.string'),
            'language.max' => __('customer::validation.language.max'),
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
            'message' => __('customer::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
