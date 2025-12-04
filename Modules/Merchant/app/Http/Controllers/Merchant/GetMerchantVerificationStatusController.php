<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;

/**
 * Get Merchant Verification Status Controller
 *
 * Handles getting verification status for a specific merchant
 */
class GetMerchantVerificationStatusController
{
    protected IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Get verification status for a specific merchant
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
            'message' => __('merchant::controller.merchant.verification.status_retrieved'),
            'data' => [
                'merchant_id' => $merchant->id,
                'is_verified' => $merchant->is_verified,
                'verification_status' => $merchant->verification_status,
                'documents' => $merchant->documents, // Polymorphic relationship
            ],
        ], 200);
    }
}
