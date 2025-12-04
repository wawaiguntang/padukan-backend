<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Update Merchant Address Controller
 *
 * Handles updating address for a specific merchant (create if not exists)
 */
class UpdateMerchantAddressController
{
    /**
     * Update address for a specific merchant
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

        // Validate that merchant doesn't already have an address (1:1 relationship)
        if ($merchant->address && $request->isMethod('post')) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.address.already_exists'),
            ], 409);
        }

        // Validate input
        $validated = $request->validate([
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Update or create address
        $address = $merchant->address()->updateOrCreate(
            ['merchant_id' => $merchantId],
            $validated
        );

        $message = $merchant->address ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.address.' . $message),
            'data' => $address,
        ], $merchant->address ? 200 : 201);
    }
}
