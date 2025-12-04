<?php

namespace Modules\Merchant\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Merchant\Services\Profile\IProfileService;

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
            'avatar' => $this->when($this->avatar, function () {
                return app(IProfileService::class)->getAvatarUrl($this->avatar);
            }),
            'avatar_path' => $this->avatar,
            'gender' => $this->gender?->value,
            'gender_label' => $this->gender?->label(),
            'language' => $this->language,
            'is_verified' => $this->is_verified,
            'verification_status' => $this->verification_status?->value,
            'verification_status_label' => $this->verification_status?->label(),
            'max_merchant' => $this->max_merchant,
            'current_merchants' => $this->whenLoaded('merchants', function () {
                return $this->merchants->count();
            }, function () {
                return $this->merchants()->count();
            }),
            'available_merchant_slots' => $this->max_merchant - ($this->whenLoaded('merchants', function () {
                return $this->merchants->count();
            }, function () {
                return $this->merchants()->count();
            })),
            'can_create_merchant' => ($this->max_merchant - ($this->whenLoaded('merchants', function () {
                return $this->merchants->count();
            }, function () {
                return $this->merchants()->count();
            }))) > 0,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'merchants' => MerchantResource::collection($this->whenLoaded('merchants')),
            'documents' => DocumentResource::collection($this->whenLoaded('documents')),
        ];
    }
}
