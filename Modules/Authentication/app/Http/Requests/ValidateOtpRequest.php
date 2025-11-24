<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate OTP Request
 *
 * Handles validation for OTP validation
 */
class ValidateOtpRequest extends FormRequest
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
            'user_id' => ['required', 'string', 'uuid'],
            'type' => ['required', 'string', 'in:phone,email'],
            'token' => ['required', 'string', 'regex:/^\d{6}$/'],
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
            'user_id' => __('authentication::validation.attributes.user_id'),
            'type' => __('authentication::validation.attributes.type'),
            'token' => __('authentication::validation.attributes.token'),
        ];
    }
}