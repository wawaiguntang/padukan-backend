<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;

/**
 * Index Address Controller
 *
 * Handles customer address listing operations
 */
class IndexAddressController
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
     * Get customer addresses
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get or create profile
        $profile = $this->profileRepository->findByUserId($user->id);
        if (!$profile) {
            $profile = $this->profileService->createProfile($user->id, []);
        }

        $addresses = $this->addressRepository->findByProfileId($profile->id);

        return response()->json([
            'status' => true,
            'message' => __('customer::address.retrieved_successfully'),
            'data' => AddressResource::collection($addresses),
        ]);
    }
}
