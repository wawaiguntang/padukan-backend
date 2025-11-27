<?php

namespace App\Shared\Profile\Services;

use Modules\Profile\Models\Profile;
use Modules\Profile\Repositories\Profile\IProfileRepository;

/**
 * Profile Service Implementation
 *
 * This class handles profile business logic operations.
 */
class ProfileService implements IProfileService
{
    /**
     * The profile repository instance
     *
     * @var IProfileRepository
     */
    protected IProfileRepository $profileRepository;

    /**
     * Constructor
     *
     * @param IProfileRepository $profileRepository The profile repository instance
     */
    public function __construct(IProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrCreateProfile(string $userId): Profile
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            $profile = $this->profileRepository->create([
                'user_id' => $userId,
                'language' => 'id', // Default language
            ]);
        }

        return $profile;
    }

    /**
     * {@inheritDoc}
     */
    public function updateProfile(string $userId, array $data): Profile
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new \Exception("Profile not found for user {$userId}");
        }

        $this->profileRepository->update($profile->id, $data);

        // Return fresh data
        return $this->profileRepository->findById($profile->id);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileWithRelations(string $userId): ?Profile
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return null;
        }

        // Load relationships
        return $profile->load([
            'addresses',
            'driverProfile.vehicles.documents',
            'driverProfile.documents',
            'merchantProfile.banks',
            'merchantProfile.address',
            'merchantProfile.documents',
            'customerProfile.documents',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteProfile(string $userId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->delete($profile->id);
    }

    /**
     * {@inheritDoc}
     */
    public function hasProfile(string $userId): bool
    {
        return $this->profileRepository->existsByUserId($userId);
    }
}