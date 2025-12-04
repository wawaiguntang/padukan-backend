<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy;
use Modules\Merchant\Http\Resources\MerchantResource;

/**
 * Get Merchant Controller
 *
 * Handles retrieving a specific merchant with ownership validation
 */
class GetMerchantController
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
     * Get a specific merchant
     */
    public function __invoke(Request $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate ownership using policy
        if (!$this->merchantOwnershipPolicy->ownsMerchant($user->id, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Get merchant with settings
        $merchant = $this->merchantService->getMerchantWithSettings($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.retrieved_successfully'),
            'data' => new MerchantResource($merchant),
        ]);
    }
}
