<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;
use Modules\Merchant\Http\Resources\DocumentResource;

/**
 * Get Merchant Verification Status Controller
 *
 * Handles getting verification status for a specific merchant
 */
class GetMerchantVerificationStatusController
{
    protected IMerchantService $merchantService;

    protected IProfileService $profileService;

    public function __construct(IMerchantService $merchantService, IProfileService $profileService)
    {
        $this->merchantService = $merchantService;
        $this->profileService = $profileService;
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

        $profile = $this->profileService->getProfileByUserId($user->id);

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
                'documents' => DocumentResource::collection($this->merchantService->getMerchantDocuments($merchantId)),
                'can_submit' => $merchant->verification_status === \Modules\Merchant\Enums\VerificationStatusEnum::PENDING,
                'can_resubmit' => $merchant->verification_status === \Modules\Merchant\Enums\VerificationStatusEnum::REJECTED,
            ],
        ], 200);
    }
}
