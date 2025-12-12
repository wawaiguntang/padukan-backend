<?php

namespace Modules\Customer\Repositories\Profile;

use Modules\Customer\Enums\GenderEnum;
use Modules\Customer\Models\Profile;

/**
 * Profile Repository Implementation
 *
 * This class handles all profile-related database operations
 * for the customer module.
 */
class ProfileRepository implements IProfileRepository
{
    /**
     * The Profile model instance
     *
     * @var Profile
     */
    protected Profile $model;

    /**
     * Constructor
     *
     * @param Profile $model The Profile model instance
     */
    public function __construct(Profile $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByUserId(string $userId): ?Profile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Profile
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Profile
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(string $id, array $data): bool
    {
        $profile = $this->model->find($id);

        if (!$profile) {
            return false;
        }

        return $profile->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        $profile = $this->model->find($id);

        if (!$profile) {
            return false;
        }

        return $profile->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function existsByUserId(string $userId): bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function updateGender(string $id, GenderEnum $gender): bool
    {
        return $this->update($id, ['gender' => $gender]);
    }
}
