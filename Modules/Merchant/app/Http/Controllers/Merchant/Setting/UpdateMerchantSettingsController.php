<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Update Merchant Settings Controller
 *
 * Handles updating settings for a specific merchant
 */
class UpdateMerchantSettingsController
{
    /**
     * Update settings for a specific merchant
     */
    public function __invoke(Request $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        $merchant = app(\Modules\Merchant\Services\Merchant\IMerchantService::class)
            ->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        $profile = app(\Modules\Merchant\Services\Profile\IProfileService::class)
            ->getProfileByUserId($user->id);

        if (!$profile || $merchant->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Validate input
        $validated = $request->validate([
            'delivery_enabled' => 'boolean',
            'delivery_radius_km' => 'integer|min:1|max:50',
            'minimum_order_amount' => 'numeric|min:0',
            'auto_accept_orders' => 'boolean',
            'preparation_time_minutes' => 'integer|min:1|max:120',
            'notifications_enabled' => 'boolean',
        ]);

        // Update or create settings
        $settings = $merchant->settings()->updateOrCreate(
            ['merchant_id' => $merchantId],
            $validated
        );

        $message = isset($merchant->settings()->first()->id) ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.settings.' . $message),
            'data' => $settings,
        ], isset($merchant->settings()->first()->id) ? 200 : 201);
    }
}
