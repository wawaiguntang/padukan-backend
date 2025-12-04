<?php

namespace Modules\Merchant\Http\Controllers\Merchant\Address;

use Illuminate\Http\JsonResponse;
use Modules\Merchant\Services\Merchant\IMerchantService;
use Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy;
use Modules\Setting\Policies\AddressManagement\IAddressManagementPolicy;
use Modules\Merchant\Http\Requests\Merchant\Address\UpdateMerchantAddressRequest;
use Modules\Merchant\Enums\VerificationStatusEnum;

/**
 * Update Merchant Address Controller
 *
 * Handles updating address for a specific merchant (create if not exists)
 */
class UpdateMerchantAddressController
{
    protected IMerchantService $merchantService;
    protected IMerchantOwnershipPolicy $merchantOwnershipPolicy;
    protected IAddressManagementPolicy $addressManagementPolicy;

    public function __construct(
        IMerchantService $merchantService,
        IMerchantOwnershipPolicy $merchantOwnershipPolicy,
        IAddressManagementPolicy $addressManagementPolicy
    ) {
        $this->merchantService = $merchantService;
        $this->merchantOwnershipPolicy = $merchantOwnershipPolicy;
        $this->addressManagementPolicy = $addressManagementPolicy;
    }

    /**
     * Update address for a specific merchant
     */
    public function __invoke(UpdateMerchantAddressRequest $request, string $merchantId): JsonResponse
    {
        $user = $request->authenticated_user;

        // Validate merchant ownership
        if (!$this->merchantOwnershipPolicy->ownsMerchant($user->id, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.access_denied'),
            ], 403);
        }

        // Get merchant for address operations
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.merchant.not_found'),
            ], 404);
        }

        // Check if merchant already has an address (1:1 relationship)
        $existingAddress = $this->merchantService->getMerchantAddress($merchantId);

        // Get validated data from form request
        $validated = $request->validated();

        // Additional validation using address management policy
        $addressValidationErrors = $this->addressManagementPolicy->validateAddressData($validated);
        if (!empty($addressValidationErrors)) {
            return response()->json([
                'status' => false,
                'message' => __('merchant::controller.address.validation_failed'),
                'errors' => $addressValidationErrors,
            ], 422);
        }

        // Update or create address
        $address = $this->merchantService->updateMerchantAddress($merchantId, $validated);

        // Set verification status to PENDING when address is updated
        $this->merchantService->updateMerchant($merchantId, [
            'verification_status' => VerificationStatusEnum::PENDING
        ]);

        $isUpdate = $existingAddress !== null;
        $message = $isUpdate ? 'updated_successfully' : 'created_successfully';

        return response()->json([
            'status' => true,
            'message' => __('merchant::controller.address.' . $message),
            'data' => $address,
        ], $isUpdate ? 200 : 201);
    }
}
