<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Services\Address\IAddressService;

/**
 * Index Address Controller
 *
 * Handles customer address listing operations
 */
class IndexAddressController
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
     * Get customer addresses
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->authenticated_user;

        // Get or create profile using service
        $profile = $this->addressService->getOrCreateProfile($user->id, []);

        $addresses = $this->addressService->getAddressesByProfileId($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::address.retrieved_successfully'),
            'data' => AddressResource::collection($addresses),
        ]);
    }
}
