<?php

namespace Modules\Profile\Services\Customer;

use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Profile\Repositories\Address\IAddressRepository;
use Modules\Profile\Policies\ProfileOwnership\IProfileOwnershipPolicy;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\AddressNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

class CustomerAddressService implements ICustomerAddressService
{
    private IProfileRepository $profileRepository;
    private IAddressRepository $addressRepository;
    private IProfileOwnershipPolicy $ownershipPolicy;

    public function __construct(
        IProfileRepository $profileRepository,
        IAddressRepository $addressRepository,
        IProfileOwnershipPolicy $ownershipPolicy
    ) {
        $this->profileRepository = $profileRepository;
        $this->addressRepository = $addressRepository;
        $this->ownershipPolicy = $ownershipPolicy;
    }

    public function getAddresses(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException('profile_not_found');
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $addresses = $this->addressRepository->getByProfileId($profile->id);

        return [
            'profile' => $profile,
            'addresses' => $addresses,
        ];
    }

    public function getAddress(string $userId, string $addressId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $address = $this->addressRepository->findById($addressId);

        if (!$address || $address->profile_id !== $profile->id) {
            throw new AddressNotFoundException($addressId);
        }

        return [
            'profile' => $profile,
            'address' => $address,
        ];
    }

    public function createAddress(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $data['profile_id'] = $profile->id;
        $address = $this->addressRepository->create($data);

        return [
            'profile' => $profile,
            'address' => $address,
        ];
    }

    public function updateAddress(string $userId, string $addressId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $address = $this->addressRepository->findById($addressId);

        if (!$address || $address->profile_id !== $profile->id) {
            throw new AddressNotFoundException($addressId);
        }

        $success = $this->addressRepository->update($addressId, $data);

        if (!$success) {
            throw new \Exception(__('profile::validation.update_failed'));
        }

        $updatedAddress = $this->addressRepository->findById($addressId);

        return [
            'profile' => $profile,
            'address' => $updatedAddress,
        ];
    }

    public function deleteAddress(string $userId, string $addressId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException('profile_not_found');
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $address = $this->addressRepository->findById($addressId);

        if (!$address || $address->profile_id !== $profile->id) {
            throw new AddressNotFoundException($addressId);
        }

        return $this->addressRepository->delete($addressId);
    }
}