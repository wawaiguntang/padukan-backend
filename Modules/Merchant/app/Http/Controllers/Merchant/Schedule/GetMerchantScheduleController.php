<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Schedule;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;

/**
 * Get Merchant Schedule Controller
 *
 * Handles getting schedule for a specific merchant
 */
class GetMerchantScheduleController
{
    protected IMerchantService $merchantService;
    protected IProfileService $profileService;

    public function __construct(
        IMerchantService $merchantService,
        IProfileService $profileService
    ) {
        $this->merchantService = $merchantService;
        $this->profileService = $profileService;
    }

    /**
     * Get schedule for a specific merchant
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

        $schedule = $this->merchantService->getMerchantSchedule($merchantId);

        if (!$schedule) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.schedule.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.schedule.retrieved_successfully'),
            'data' => $schedule,
        ], 200);
    }
}
