<?php

namespace Modules\Customer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Http\Requests\AddressCreateRequest;
use Modules\Customer\Http\Requests\AddressUpdateRequest;
use Modules\Customer\Http\Resources\AddressResource;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;

/**
 * Address Controller
 *
 * Handles customer address operations with auto-profile creation
 */
class AddressController
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
    public function index(Request $request): JsonResponse
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

    /**
     * Create new address
     */
    public function store(AddressCreateRequest $request): JsonResponse
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

    /**
     * Get specific address
     */
    public function show(Request $request, string $id): JsonResponse
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

    /**
     * Update address
     */
    public function update(AddressUpdateRequest $request, string $id): JsonResponse
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

    /**
     * Delete address
     */
    public function destroy(Request $request, string $id): JsonResponse
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

        $deleted = $this->addressRepository->delete($id);

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

    /**
     * Set address as primary
     */
    public function setPrimary(Request $request, string $id): JsonResponse
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

        $updated = $this->addressRepository->setAsPrimary($id);

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
