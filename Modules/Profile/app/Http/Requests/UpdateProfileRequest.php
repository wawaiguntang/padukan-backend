<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Profile\Enums\GenderEnum;

/**
 * Update Profile Request
 *
 * Handles validation for profile updates
 */
class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB
            'gender' => ['nullable', 'in:' . implode(',', array_column(GenderEnum::cases(), 'value'))],
            'language' => ['nullable', 'string', 'in:id,en'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('profile::validation.attributes.first_name'),
            'last_name' => __('profile::validation.attributes.last_name'),
            'avatar' => __('profile::validation.attributes.avatar'),
            'gender' => __('profile::validation.attributes.gender'),
            'language' => __('profile::validation.attributes.language'),
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
            'success' => false,
            'message' => __('profile::validation.failed'),
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}