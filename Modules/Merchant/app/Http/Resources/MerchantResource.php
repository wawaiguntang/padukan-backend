<?php

namespace Modules\Merchant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'business_name' => $this->business_name,
            'business_description' => $this->business_description,
            'business_category' => $this->business_category,
            'slug' => $this->slug,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'address_details' => $this->whenLoaded('address'),
            'settings' => $this->whenLoaded('settings'),
            'schedule' => $this->whenLoaded('schedules'),
        ];
    }
}
