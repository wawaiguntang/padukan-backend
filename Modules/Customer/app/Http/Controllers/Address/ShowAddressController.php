<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Services\Address\IAddressService;

/**
 * Show Address Controller
 *
 * Handles retrieving specific customer addresses
 */
class ShowAddressController
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
     * Get specific address
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $user = $request->authenticated_user;

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

        return response()->json([
            'status' => true,
            'message' => __('customer::controller.address.retrieved_successfully'),
            'data' => new AddressResource($address),
        ]);
    }
}
