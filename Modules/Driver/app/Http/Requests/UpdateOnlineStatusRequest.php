<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Online Status Request Validation
 */
class UpdateOnlineStatusRequest extends FormRequest
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
        $rules = [
            'online_status' => 'required|in:online,offline',
        ];

        // When going online, active_service and vehicle_id are required
        if ($this->input('online_status') === 'online') {
            $rules['active_service'] = 'required|in:food,ride,car,send,mart';
            $rules['vehicle_id'] = 'required|uuid|exists:driver.vehicles,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'online_status.required' => __('driver::validation.status.online_status.required'),
            'online_status.in' => __('driver::validation.status.online_status.in'),
        ];

        // Add active_service and vehicle_id messages when going online
        if ($this->input('online_status') === 'online') {
            $messages['active_service.required'] = __('driver::validation.status.active_service.required');
            $messages['active_service.in'] = __('driver::validation.status.active_service.in');
            $messages['vehicle_id.required'] = __('driver::status.vehicle_id.required');
            $messages['vehicle_id.uuid'] = __('driver::status.vehicle_id.uuid');
            $messages['vehicle_id.exists'] = __('driver::status.vehicle_id.exists');
        }

        return $messages;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'online_status' => __('driver::attributes.status.online_status'),
        ];

        // Add active_service and vehicle_id attributes when going online
        if ($this->input('online_status') === 'online') {
            $attributes['active_service'] = __('driver::attributes.status.active_service');
            $attributes['vehicle_id'] = __('driver::status.vehicle_id');
        }

        return $attributes;
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
