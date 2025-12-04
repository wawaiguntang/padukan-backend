<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Setting\IMerchantSettingService;
use Modules\Merchant\Services\Profile\IProfileService;

/**
 * Get Merchant Settings Controller
 *
 * Handles getting settings for a specific merchant
 */
class GetMerchantSettingsController
{
    protected IMerchantService $merchantService;
    protected IMerchantSettingService $merchantSettingService;
    protected IProfileService $profileService;

    public function __construct(
        IMerchantService $merchantService,
        IMerchantSettingService $merchantSettingService,
        IProfileService $profileService
    ) {
        $this->merchantService = $merchantService;
        $this->merchantSettingService = $merchantSettingService;
        $this->profileService = $profileService;
    }

    /**
     * Get settings for a specific merchant
     */
    public function __invoke(Request $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        $profile = $this->profileService->getProfileByUserId($user->id);

        if (!$profile || $merchant->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        $settings = $this->merchantSettingService->getSettingsByMerchantId($merchantId);

        if (!$settings) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.settings.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.settings.retrieved_successfully'),
            'data' => $settings,
        ], 200);
    }
}
