<?php

namespace Modules\Merchant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Profile Resource
 *
 * Formats profile data for API responses
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->when($this->first_name || $this->last_name, function () {
                return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
            }),
            'avatar' => $this->avatar,
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->label(),
            'language' => $this->language,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status?->value,
            'verification_status_label' => $this->verification_status?->label(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
        ];
    }
}
