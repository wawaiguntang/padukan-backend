<?php

namespace Modules\Merchant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'first_name.string' => __('merchant::validation.first_name.string'),
            'first_name.max' => __('merchant::validation.first_name.max'),
            'last_name.string' => __('merchant::validation.last_name.string'),
            'last_name.max' => __('merchant::validation.last_name.max'),
            'avatar_file.file' => __('merchant::validation.avatar_file.file'),
            'avatar_file.mimes' => __('merchant::validation.avatar_file.mimes'),
            'avatar_file.max' => __('merchant::validation.avatar_file.max'),
            'gender.in' => __('merchant::validation.gender.in'),
            'language.string' => __('merchant::validation.language.string'),
            'language.max' => __('merchant::validation.language.max'),
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
            'message' => __('merchant::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
