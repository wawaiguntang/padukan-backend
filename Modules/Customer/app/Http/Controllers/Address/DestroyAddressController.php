<?php

namespace Modules\Customer\Http\Controllers\Address;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;

/**
 * Destroy Address Controller
 *
 * Handles deleting customer addresses
 */
class DestroyAddressController
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
     * Delete address
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
}
