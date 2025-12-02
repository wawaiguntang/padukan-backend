<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Services\Address\IAddressService;

/**
 * Destroy Address Controller
 *
 * Handles deleting customer addresses
 */
class DestroyAddressController
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
     * Delete address
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $user = $request->authenticated_user;

        $address = $this->addressService->getAddressById($id);

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.not_found'),
            ], 404);
        }

        // Check ownership using service
        if (!$this->addressService->isAddressOwnedByUser($id, $user->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.access_denied'),
            ], 403);
        }

        $deleted = $this->addressService->deleteAddress($id);

        if (!$deleted) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.delete_failed'),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => __('customer::address.deleted_successfully'),
        ]);
    }
}
