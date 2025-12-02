<?php

namespace Modules\Customer\Services\Address;

use Modules\Customer\Models\Address;
use Modules\Customer\Models\Profile;
use Modules\Customer\Repositories\Address\IAddressRepository;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\Profile\IProfileService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Address Service
 *
 * Implements address business logic operations
 */
class AddressService implements IAddressService
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
     * {@inheritdoc}
     */
    public function createAddress(string $profileId, array $data): Address
    {
        return $this->addressRepository->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressesByProfileId(string $profileId): Collection
    {
        return $this->addressRepository->findByProfileId($profileId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressById(string $id): ?Address
    {
        return $this->addressRepository->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAddress(string $id, array $data): bool
    {
        return $this->addressRepository->update($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAddress(string $id): bool
    {
        return $this->addressRepository->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function setAsPrimary(string $id): bool
    {
        return $this->addressRepository->setAsPrimary($id);
    }

    /**
     * {@inheritdoc}
     */
    public function isAddressOwnedByUser(string $addressId, string $userId): bool
    {
        $address = $this->addressRepository->findById($addressId);

        if (!$address) {
            return false;
        }

        $profile = $this->profileRepository->findByUserId($userId);

        return $profile && $address->profile_id === $profile->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrCreateProfile(string $userId, array $defaultData = []): Profile
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            $profile = $this->profileService->createProfile($userId, $defaultData);
        }

        return $profile;
    }
}
