<?php

namespace Modules\Customer\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Address Resource
 *
 * Formats address data for API responses
 */
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'label' => $this->label,
            'street' => $this->street,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_primary' => $this->is_primary,
            'full_address' => $this->when($this->street && $this->city, function () {
                return $this->street . ', ' . $this->city .
                    ($this->province ? ', ' . $this->province : '') .
                    ($this->postal_code ? ' ' . $this->postal_code : '');
            }),
            'coordinates' => $this->when($this->latitude && $this->longitude, function () {
                return [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
