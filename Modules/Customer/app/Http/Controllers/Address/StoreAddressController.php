<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\AddressCreateRequest;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;

/**
 * Store Address Controller
 *
 * Handles creating new customer addresses
 */
class StoreAddressController
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
     * Profile service instance
     */
    protected IProfileService $profileService;

    /**
     * Constructor
     */
    public function __construct(
        IAddressRepository $addressRepository,
        IProfileRepository $profileRepository,
        IProfileService $profileService
    ) {
        $this->addressRepository = $addressRepository;
        $this->profileRepository = $profileRepository;
        $this->profileService = $profileService;
    }

    /**
     * Create new address
     */
    public function __invoke(AddressCreateRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Get or create profile
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        // Add profile_id to validated data
        $validated['profile_id'] = $profile->id;

        $address = $this->addressRepository->create($validated);

        return response()->json([
            'status' => true,
            'message' => __('customer::address.created_successfully'),
            'data' => new AddressResource($address),
        ], 201);
    }
}
