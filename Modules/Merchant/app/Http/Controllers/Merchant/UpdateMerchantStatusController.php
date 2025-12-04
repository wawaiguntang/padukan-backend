<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Http\Requests\Merchant\UpdateMerchantStatusRequest;

/**
 * Update Merchant Status Controller
 *
 * Handles updating status (open/closed) for a specific merchant
 */
class UpdateMerchantStatusController
{
    protected IMerchantService $merchantService;

    public function __construct(IMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Update status for a specific merchant
     */
    public function __invoke(UpdateMerchantStatusRequest $request, string $merchantId): JsonResponse
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

        // Validate status
        $status = $request->input('status');
        $validStatuses = [
            \Modules\Merchant\Enums\MerchantStatusEnum::OPEN->value,
            \Modules\Merchant\Enums\MerchantStatusEnum::CLOSED->value,
            \Modules\Merchant\Enums\MerchantStatusEnum::TEMPORARILY_CLOSED->value,
        ];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.status.invalid'),
            ], 422);
        }

        // Update status
        $updated = $this->merchantService->updateStatus($merchantId, \Modules\Merchant\Enums\MerchantStatusEnum::from($status));

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.status.update_failed'),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.status.updated_successfully'),
        ], 200);
    }
}
