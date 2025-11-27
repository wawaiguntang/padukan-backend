<?php

namespace Modules\Profile\Services\Address;

use Modules\Profile\Models\Address;
use Modules\Profile\Repositories\Address\IAddressRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Address Service Implementation
 *
 * Handles address management business logic with validation and caching
 */
class AddressService implements IAddressService
{
    protected IAddressRepository $addressRepository;

    public function __construct(IAddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function getProfileAddresses(string $profileId): Collection
    {
        try {
            return $this->addressRepository->getByProfileId($profileId);
        } catch (\Exception $e) {
            Log::error('Failed to get profile addresses', [
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to retrieve addresses');
        }
    }

    public function createAddress(string $profileId, array $data): Address
    {
        try {
            // Validate data
            $this->validateAddressData($data);

            // If this is set as primary, ensure only one primary address
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->ensureSinglePrimaryAddress($profileId);
            }

            $data['profile_id'] = $profileId;

            return $this->addressRepository->create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create address', [
                'profile_id' => $profileId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to create address');
        }
    }

    public function updateAddress(string $addressId, array $data): bool
    {
        try {
            // Validate ownership
            $address = $this->addressRepository->findById($addressId);
            if (!$address) {
                throw new \Exception('Address not found');
            }

            // Validate data
            $this->validateAddressData($data, false);

            // If setting as primary, ensure only one primary address
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->ensureSinglePrimaryAddress($address->profile_id, $addressId);
            }

            return $this->addressRepository->update($addressId, $data);
        } catch (\Exception $e) {
            Log::error('Failed to update address', [
                'address_id' => $addressId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to update address');
        }
    }

    public function deleteAddress(string $addressId): bool
    {
        try {
            $address = $this->addressRepository->findById($addressId);
            if (!$address) {
                throw new \Exception('Address not found');
            }

            return $this->addressRepository->delete($addressId);
        } catch (\Exception $e) {
            Log::error('Failed to delete address', [
                'address_id' => $addressId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to delete address');
        }
    }

    public function setPrimaryAddress(string $addressId, string $profileId): bool
    {
        try {
            // Validate ownership
            if (!$this->validateAddressOwnership($addressId, $profileId)) {
                throw new \Exception('Address does not belong to profile');
            }

            return $this->addressRepository->setAsPrimary($addressId, $profileId);
        } catch (\Exception $e) {
            Log::error('Failed to set primary address', [
                'address_id' => $addressId,
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to set primary address');
        }
    }

    public function getPrimaryAddress(string $profileId): ?Address
    {
        try {
            return $this->addressRepository->getPrimaryAddress($profileId);
        } catch (\Exception $e) {
            Log::error('Failed to get primary address', [
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function validateAddressOwnership(string $addressId, string $profileId): bool
    {
        $address = $this->addressRepository->findById($addressId);
        return $address && $address->profile_id === $profileId;
    }

    /**
     * Validate address data
     */
    protected function validateAddressData(array $data, bool $isCreate = true): void
    {
        // Required fields for create
        if ($isCreate) {
            if (empty($data['street']) || empty($data['city']) || empty($data['province'])) {
                throw new \Exception('Street, city, and province are required');
            }
        }

        // Validate coordinates if provided
        if (isset($data['latitude'])) {
            if ($data['latitude'] < -90 || $data['latitude'] > 90) {
                throw new \Exception('Invalid latitude');
            }
        }

        if (isset($data['longitude'])) {
            if ($data['longitude'] < -180 || $data['longitude'] > 180) {
                throw new \Exception('Invalid longitude');
            }
        }

        // Validate type if provided
        if (isset($data['type'])) {
            $validTypes = array_column(\Modules\Profile\Enums\AddressTypeEnum::cases(), 'value');
            if (!in_array($data['type'], $validTypes)) {
                throw new \Exception('Invalid address type');
            }
        }
    }

    /**
     * Ensure only one primary address per profile
     */
    protected function ensureSinglePrimaryAddress(string $profileId, string $excludeAddressId = null): void
    {
        $addresses = $this->addressRepository->getByProfileId($profileId);

        foreach ($addresses as $address) {
            if ($excludeAddressId && $address->id === $excludeAddressId) {
                continue;
            }

            if ($address->is_primary) {
                $this->addressRepository->update($address->id, ['is_primary' => false]);
            }
        }
    }
}