<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Services\Address\IAddressService;

/**
 * Set Primary Address Controller
 *
 * Handles setting customer addresses as primary
 */
class SetPrimaryAddressController
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
     * Set address as primary
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

        $updated = $this->addressService->setAsPrimary($id);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.primary_update_failed'),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => __('customer::address.primary_updated_successfully'),
        ]);
    }
}
