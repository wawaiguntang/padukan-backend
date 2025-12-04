<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Schedule;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy;
use Modules\Merchant\Http\Requests\Merchant\Schedule\UpdateMerchantScheduleRequest;

/**
 * Update Merchant Schedule Controller
 *
 * Handles updating schedule for a specific merchant
 */
class UpdateMerchantScheduleController
{
    protected IMerchantService $merchantService;
    protected IMerchantOwnershipPolicy $merchantOwnershipPolicy;

    public function __construct(
        IMerchantService $merchantService,
        IMerchantOwnershipPolicy $merchantOwnershipPolicy
    ) {
        $this->merchantService = $merchantService;
        $this->merchantOwnershipPolicy = $merchantOwnershipPolicy;
    }

    /**
     * Update schedule for a specific merchant
     */
    public function __invoke(UpdateMerchantScheduleRequest $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        if (!$this->merchantOwnershipPolicy->ownsMerchant($user->id, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Get merchant for schedule operations
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        // Get validated data from form request
        $validated = $request->validated();

        // Update schedule
        $updated = $this->merchantService->updateSchedule($merchantId, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.schedule.update_failed'),
            ], 500);
        }

        // Get updated merchant with schedule
        $updatedMerchant = $this->merchantService->getMerchantById($merchantId);

        $message = $merchant->schedules ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.schedule.' . $message),
            'data' => $updatedMerchant,
        ], $merchant->schedules ? 200 : 201);
    }
}
