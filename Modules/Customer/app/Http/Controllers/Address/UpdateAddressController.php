<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\AddressUpdateRequest;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Services\Address\IAddressService;

/**
 * Update Address Controller
 *
 * Handles updating customer addresses
 */
class UpdateAddressController
{
    /**
     * Address service instance
     */
    protected IAddressService $addressService;

    /**
     * Constructor
     */
    public function __construct(IAddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Update address
     */
    public function __invoke(AddressUpdateRequest $request, string $id): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        $address = $this->addressService->getAddressById($id);

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.address.not_found'),
            ], 404);
        }

        // Check ownership using service
        if (!$this->addressService->isAddressOwnedByUser($id, $user->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.address.access_denied'),
            ], 403);
        }

        $updated = $this->addressService->updateAddress($id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('customer::controller.address.update_failed'),
            ], 500);
        }

        $updatedAddress = $this->addressService->getAddressById($id);

        return response()->json([
            'status' => true,
            'message' => __('customer::controller.address.updated_successfully'),
            'data' => new AddressResource($updatedAddress),
        ]);
    }
}
