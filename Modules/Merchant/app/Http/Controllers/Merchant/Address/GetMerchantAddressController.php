<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Get Merchant Address Controller
 *
 * Handles getting address for a specific merchant
 */
class GetMerchantAddressController
{
    /**
     * Get address for a specific merchant
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

        $address = $merchant->address;

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.address.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.address.retrieved_successfully'),
            'data' => $address,
        ], 200);
    }
}
