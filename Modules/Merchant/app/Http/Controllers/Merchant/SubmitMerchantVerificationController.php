<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;

/**
 * Submit Merchant Verification Controller
 *
 * Handles submitting verification for a specific merchant
 */
class SubmitMerchantVerificationController
{
    protected IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Submit verification for a specific merchant
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

        // Check if merchant can be verified
        if ($merchant->verification_status !== \Modules\Merchant\Enums\VerificationStatusEnum::PENDING) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.verification.cannot_submit'),
            ], 400);
        }

        // Submit verification (would check if documents exist)
        $updated = $this->merchantService->updateVerificationStatus(
            $merchantId,
            false,
            \Modules\Merchant\Enums\VerificationStatusEnum::ON_REVIEW->value
        );

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.verification.submission_failed'),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.verification.submitted_successfully'),
        ], 200);
    }
}
