<?php

namespace Modules\Merchant\Services\ForShare;

use App\Shared\Merchant\Services\IMerchantService as ISharedMerchantService;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Models\Merchant;

class MerchantService implements ISharedMerchantService
{
    private IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Convert Merchant model to array
     */
    private function merchantToArray(Merchant $merchant): array
    {
        return [
            'id' => $merchant->id,
            'profile_id' => $merchant->profile_id,
            'business_name' => $merchant->business_name,
            'business_description' => $merchant->business_description,
            'business_category' => $merchant->business_category?->value,
            'slug' => $merchant->slug,
            'phone' => $merchant->phone,
            'email' => $merchant->email,
            'website' => $merchant->website,
            'logo' => $merchant->logo,
            'logo_url' => $merchant->logo_url,
            'street' => $merchant->street,
            'city' => $merchant->city,
            'province' => $merchant->province,
            'country' => $merchant->country,
            'postal_code' => $merchant->postal_code,
            'latitude' => $merchant->latitude,
            'longitude' => $merchant->longitude,
            'is_verified' => $merchant->is_verified,
            'verification_status' => $merchant->verification_status?->value,
            'is_active' => $merchant->is_active,
            'status' => $merchant->status?->value,
            'created_at' => $merchant->created_at,
            'updated_at' => $merchant->updated_at,
        ];
    }


    /**
     * Check if user owns a merchant
     */
    public function checkOwnership(string $userId, string $merchantId): bool
    {
        $merchant = $this->merchantService->getMerchantById($merchantId);
        return $merchant && $merchant->profile_id === $userId;
    }

    /**
     * Get merchant by ID
     */
    public function getMerchantById(string $id): ?array
    {
        $merchant = $this->merchantService->getMerchantById($id);
        return $merchant ? $this->merchantToArray($merchant) : null;
    }

    /**
     * Get merchant settings
     */
    public function getMerchantSetting(string $merchantId): ?array
    {
        $merchant = $this->merchantService->getMerchantWithSettings($merchantId);

        if (!$merchant || !$merchant->settings) {
            return null;
        }

        return [
            'id' => $merchant->settings->id,
            'merchant_id' => $merchant->settings->merchant_id,
            'delivery_enabled' => $merchant->settings->delivery_enabled,
            'delivery_radius_km' => $merchant->settings->delivery_radius_km,
            'minimum_order_amount' => $merchant->settings->minimum_order_amount,
            'auto_accept_orders' => $merchant->settings->auto_accept_orders,
            'preparation_time_minutes' => $merchant->settings->preparation_time_minutes,
            'notifications_enabled' => $merchant->settings->notifications_enabled,
            'use_inventory' => $merchant->settings->use_inventory,
            'created_at' => $merchant->settings->created_at,
            'updated_at' => $merchant->settings->updated_at,
        ];
    }

    /**
     * Get merchant schedule
     */
    public function getMerchantSchedule(string $merchantId): ?array
    {
        return $this->merchantService->getMerchantSchedule($merchantId);
    }
}
