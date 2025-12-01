<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\AddressUpdateRequest;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;

/**
 * Update Address Controller
 *
 * Handles updating customer addresses
 */
class UpdateAddressController
{
    /**
     * Address repository instance
     */
    protected IAddressRepository $addressRepository;

    /**
     * Profile repository instance
     */
    protected IProfileRepository $profileRepository;

    /**
     * Constructor
     */
    public function __construct(
        IAddressRepository $addressRepository,
        IProfileRepository $profileRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * Update address
     */
    public function __invoke(AddressUpdateRequest $request, string $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $address = $this->addressRepository->findById($id);

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.not_found'),
            ], 404);
        }

        // Check ownership
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile || $address->profile_id !== $profile->id) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.access_denied'),
            ], 403);
        }

        $updated = $this->addressRepository->update($id, $validated);

        if (!$updated) {
            return response()->json([
                'status' => false,
                'message' => __('customer::address.update_failed'),
            ], 500);
        }

        $updatedAddress = $this->addressRepository->findById($id);

        return response()->json([
            'status' => true,
            'message' => __('customer::address.updated_successfully'),
            'data' => new AddressResource($updatedAddress),
        ]);
    }
}
