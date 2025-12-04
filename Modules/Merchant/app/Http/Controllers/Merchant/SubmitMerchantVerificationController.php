<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Http\Requests\Merchant\MerchantVerificationRequest;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;

/**
 * Submit Merchant Verification Controller
 *
 * Handles submitting verification for a specific merchant
 */
class SubmitMerchantVerificationController
{
    protected IMerchantService $merchantService;

    protected IProfileService $profileService;

    public function __construct(IMerchantService $merchantService, IProfileService $profileService)
    {
        $this->merchantService = $merchantService;
        $this->profileService = $profileService;
    }

    /**
     * Submit verification for a specific merchant
     */
    public function __invoke(MerchantVerificationRequest $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

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

        try {
            if ($merchant->verification_status === \Modules\Merchant\Enums\VerificationStatusEnum::PENDING) {
                $result = $this->merchantService->resubmitVerification($merchantId, [
                    'merchant_document_file' => $request->file('merchant_document_file'),
                    'merchant_document_meta' => $validated['merchant_document_meta'] ?? null,
                    'banner_file' => $request->file('banner_file'),
                    'banner_meta' => $validated['banner_meta'] ?? null,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => __('merchant::controller.merchant.verification.submitted_successfully'),
                    'data' => $result,
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('merchant::controller.merchant.verification.cannot_submit'),
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.verification.submission_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
