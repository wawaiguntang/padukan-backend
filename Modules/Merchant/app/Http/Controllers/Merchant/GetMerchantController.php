<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Http\Resources\MerchantResource;

/**
 * Get Merchant Controller
 *
 * Handles retrieving a specific merchant with ownership validation
 */
class GetMerchantController
{
    protected IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Get a specific merchant
     */
    public function __invoke(Request $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Get merchant
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        // Validate ownership - check if merchant belongs to user's profile
        $profile = app(\Modules\Merchant\Services\Profile\IProfileService::class)
            ->getProfileByUserId($user->id);

        if (!$profile || $merchant->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.retrieved_successfully'),
            'data' => new MerchantResource($merchant->load(['address', 'settings', 'schedules'])),
        ]);
    }
}
