<?php

namespace Modules\Driver\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Vehicle Update Request Validation
 */
class VehicleUpdateRequest extends FormRequest
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
        $vehicleId = $this->route('id');

        return [
            'type' => 'nullable|string|in:motorcycle,car,truck',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20|unique:driver.vehicles,license_plate,' . $vehicleId,
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.in' => __('driver::validation.vehicle.type.in'),
            'brand.max' => __('driver::validation.vehicle.brand.max'),
            'model.max' => __('driver::validation.vehicle.model.max'),
            'year.integer' => __('driver::validation.vehicle.year.integer'),
            'year.min' => __('driver::validation.vehicle.year.min'),
            'year.max' => __('driver::validation.vehicle.year.max'),
            'color.max' => __('driver::validation.vehicle.color.max'),
            'license_plate.max' => __('driver::validation.vehicle.license_plate.max'),
            'license_plate.unique' => __('driver::validation.vehicle.license_plate.unique'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => __('driver::validation.attributes.type'),
            'brand' => __('driver::validation.attributes.brand'),
            'model' => __('driver::validation.attributes.model'),
            'year' => __('driver::validation.attributes.year'),
            'color' => __('driver::validation.attributes.color'),
            'license_plate' => __('driver::validation.attributes.license_plate'),
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
