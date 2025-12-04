<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Schedule;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Update Merchant Schedule Controller
 *
 * Handles updating schedule for a specific merchant
 */
class UpdateMerchantScheduleController
{
    /**
     * Update schedule for a specific merchant
     */
    public function __invoke(Request $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        $merchant = app(\Modules\Merchant\Services\Merchant\IMerchantService::class)
            ->getMerchantById($merchantId);

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

        // Validate input
        $validated = $request->validate([
            'regular_hours' => 'required|array',
            'special_schedules' => 'nullable|array',
            'temporary_closures' => 'nullable|array',
        ]);

        // Update or create schedule
        $schedule = $merchant->schedules()->updateOrCreate(
            ['merchant_id' => $merchantId],
            $validated
        );

        $message = isset($merchant->schedules()->first()->id) ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.schedule.' . $message),
            'data' => $schedule,
        ], isset($merchant->schedules()->first()->id) ? 200 : 201);
    }
}
