<?php

namespace Modules\Merchant\Http\Requests\Merchant\Setting;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Merchant Settings Request
 *
 * Validates data for updating merchant settings
 */
class UpdateMerchantSettingsRequest extends FormRequest
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
            'delivery_enabled' => 'sometimes|boolean',
            'delivery_radius_km' => 'sometimes|integer|min:1|max:50',
            'minimum_order_amount' => 'sometimes|numeric|min:0',
            'auto_accept_orders' => 'sometimes|boolean',
            'preparation_time_minutes' => 'sometimes|integer|min:1|max:120',
            'notifications_enabled' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'delivery_enabled.boolean' => __('merchant::validation.delivery_enabled.boolean'),
            'delivery_radius_km.integer' => __('merchant::validation.delivery_radius_km.integer'),
            'delivery_radius_km.min' => __('merchant::validation.delivery_radius_km.min'),
            'delivery_radius_km.max' => __('merchant::validation.delivery_radius_km.max'),
            'minimum_order_amount.numeric' => __('merchant::validation.minimum_order_amount.numeric'),
            'minimum_order_amount.min' => __('merchant::validation.minimum_order_amount.min'),
            'auto_accept_orders.boolean' => __('merchant::validation.auto_accept_orders.boolean'),
            'preparation_time_minutes.integer' => __('merchant::validation.preparation_time_minutes.integer'),
            'preparation_time_minutes.min' => __('merchant::validation.preparation_time_minutes.min'),
            'preparation_time_minutes.max' => __('merchant::validation.preparation_time_minutes.max'),
            'notifications_enabled.boolean' => __('merchant::validation.notifications_enabled.boolean'),
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
