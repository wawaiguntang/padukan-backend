<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Services\Profile\IProfileService;

/**
 * Get Merchant Address Controller
 *
 * Handles getting address for a specific merchant
 */
class GetMerchantAddressController
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
     * Get address for a specific merchant
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

        $address = $this->merchantService->getMerchantAddress($merchantId);

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
