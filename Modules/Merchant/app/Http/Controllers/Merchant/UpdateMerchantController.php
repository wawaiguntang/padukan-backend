<?php

namespace Modules\Merchant\Http\Controllers\Merchant;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy;
use Modules\Merchant\Http\Resources\MerchantResource;
use Modules\Merchant\Http\Requests\Merchant\UpdateMerchantRequest;

/**
 * Update Merchant Controller
 *
 * Handles updating a merchant with ownership validation
 */
class UpdateMerchantController
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
     * Update a specific merchant
     */
    public function __invoke(UpdateMerchantRequest $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate ownership using policy
        if (!$this->merchantOwnershipPolicy->ownsMerchant($user->id, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Get merchant for update operations
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        // Update merchant
        $updated = $this->merchantService->updateMerchant($merchantId, $request->all());

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.update_failed'),
            ], 500);
        }

        // Get updated merchant
        $updatedMerchant = $this->merchantService->getMerchantById($merchantId);

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.merchant.updated_successfully'),
            'data' => new MerchantResource($updatedMerchant),
        ]);
    }
}
