<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Active Service Request Validation
 */
class UpdateActiveServiceRequest extends FormRequest
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
            'active_service' => 'required|in:food,ride,car,send,mart',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'active_service.required' => __('driver::validation.status.active_service.required'),
            'active_service.in' => __('driver::validation.status.active_service.in'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'active_service' => __('driver::attributes.status.active_service'),
        ];
    }

    /**
     * Handle a failed validation attempt.
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
