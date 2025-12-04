<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Setting;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Setting\IMerchantSettingService;
use Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy;
use Modules\Merchant\Http\Requests\Merchant\Setting\UpdateMerchantSettingsRequest;

/**
 * Update Merchant Settings Controller
 *
 * Handles updating settings for a specific merchant
 */
class UpdateMerchantSettingsController
{
    protected IMerchantService $merchantService;
    protected IMerchantSettingService $merchantSettingService;
    protected IMerchantOwnershipPolicy $merchantOwnershipPolicy;

    public function __construct(
        IMerchantService $merchantService,
        IMerchantSettingService $merchantSettingService,
        IMerchantOwnershipPolicy $merchantOwnershipPolicy
    ) {
        $this->merchantService = $merchantService;
        $this->merchantSettingService = $merchantSettingService;
        $this->merchantOwnershipPolicy = $merchantOwnershipPolicy;
    }

    /**
     * Update settings for a specific merchant
     */
    public function __invoke(UpdateMerchantSettingsRequest $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        if (!$this->merchantOwnershipPolicy->ownsMerchant($user->id, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Get merchant for settings operations
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        // Get validated data from form request
        $validated = $request->validated();

        // Update settings
        $updated = $this->merchantSettingService->updateSettings($merchantId, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.settings.update_failed'),
            ], 500);
        }

        // Get updated settings
        $settings = $this->merchantSettingService->getSettingsByMerchantId($merchantId);

        $message = $merchant->settings ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.settings.' . $message),
            'data' => $settings,
        ], isset($merchant->settings()->first()->id) ? 200 : 201);
    }
}
