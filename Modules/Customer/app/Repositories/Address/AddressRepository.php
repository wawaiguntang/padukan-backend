<?php

namespace Modules\Customer\Repositories\Address;

use Illuminate\Database\Eloquent\Collection;
use Modules\Customer\Enums\AddressTypeEnum;
use Modules\Customer\Models\Address;

/**
 * Address Repository Implementation
 *
 * This class handles all address-related database operations
 * for the customer module.
 */
class AddressRepository implements IAddressRepository
{
    /**
     * The Address model instance
     *
     * @var Address
     */
    protected Address $model;

    /**
     * Constructor
     *
     * @param Address $model The Address model instance
     */
    public function __construct(Address $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByProfileId(string $profileId): Collection
    {
        return $this->model->where('profile_id', $profileId)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Address
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Address
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        return $address->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        return $address->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function setAsPrimary(string $id): bool
    {
        $address = $this->model->find($id);

        if (!$address) {
            return false;
        }

        // First, set all addresses for this profile to non-primary
        $this->model->where('profile_id', $address->profile_id)->update(['is_primary' => false]);

        // Then set this address as primary
        return $address->update(['is_primary' => true]);
    }

    /**
     * {@inheritDoc}
     */
    public function findPrimaryByProfileId(string $profileId): ?Address
    {
        return $this->model
            ->where('profile_id', $profileId)
            ->where('is_primary', true)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByTypeAndProfileId(string $profileId, AddressTypeEnum $type): Collection
    {
        return $this->model
            ->where('profile_id', $profileId)
            ->where('type', $type)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function existsById(string $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function countByProfileId(string $profileId): int
    {
        return $this->model->where('profile_id', $profileId)->count();
    }
}
