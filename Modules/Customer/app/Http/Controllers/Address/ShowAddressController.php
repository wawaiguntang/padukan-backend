<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;

/**
 * Show Address Controller
 *
 * Handles retrieving specific customer addresses
 */
class ShowAddressController
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
     * Get specific address
     */
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

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

        return response()->json([
            'status' => true,
            'message' => __('customer::address.retrieved_successfully'),
            'data' => new AddressResource($address),
        ]);
    }
}
