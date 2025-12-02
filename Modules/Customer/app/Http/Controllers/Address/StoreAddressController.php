<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\AddressCreateRequest;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Services\Address\IAddressService;
use Modules\Customer\Policies\AddressManagement\IAddressManagementPolicy;

/**
 * Store Address Controller
 *
 * Handles creating new customer addresses
 */
class StoreAddressController
{
    /**
     * Address service instance
     */
    protected IAddressService $addressService;

    /**
     * Address management policy instance
     */
    protected IAddressManagementPolicy $addressManagementPolicy;

    /**
     * Constructor
     */
    public function __construct(
        IAddressService $addressService,
        IAddressManagementPolicy $addressManagementPolicy
    ) {
        $this->addressService = $addressService;
        $this->addressManagementPolicy = $addressManagementPolicy;
    }

    /**
     * Create new address
     */
    public function __invoke(AddressCreateRequest $request): JsonResponse
    {
        $user = $request->authenticated_user;
        $validated = $request->validated();

        // Get or create profile using service
        $profile = $this->addressService->getOrCreateProfile($user->id, []);

        // Add profile_id to validated data
        $validated['profile_id'] = $profile->id;

        // Check if profile can add more addresses
        if (!$this->addressManagementPolicy->canAddAddress($profile->id)) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.max_limit_reached'),
            ], 403);
        }

        // Create address using service
        $address = $this->addressService->createAddress($profile->id, $validated);

        return response()->json([
            'status' => true,
            'message' => __('customer::address.created_successfully'),
            'data' => new AddressResource($address),
        ], 201);
    }
}
