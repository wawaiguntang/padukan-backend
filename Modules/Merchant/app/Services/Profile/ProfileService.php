<?php

namespace Modules\Merchant\Services\Profile;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Modules\Merchant\Enums\GenderEnum;
use Modules\Merchant\Models\Profile;
use Modules\Merchant\Repositories\Profile\IProfileRepository;

/**
 * Profile Service Implementation
 *
 * This class handles profile business logic operations
 * for the merchant module.
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
    public function createProfile(string $userId, array $data): Profile
    {
        // Check if profile already exists
        if ($this->profileRepository->existsByUserId($userId)) {
            throw new \Exception('Profile already exists for this user');
        }

        // Prepare data with user_id
        $profileData = array_merge($data, ['user_id' => $userId]);

        return $this->profileRepository->create($profileData);
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileByUserId(string $userId): ?Profile
    {
        return $this->profileRepository->findByUserId($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateProfile(string $userId, array $data): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->update($profile->id, $data);
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
    public function updateGender(string $userId, GenderEnum $gender): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->updateGender($profile->id, $gender);
    }

    /**
     * {@inheritDoc}
     */
    public function hasProfile(string $userId): bool
    {
        return $this->profileRepository->existsByUserId($userId);
    }

    /**
     * {@inheritDoc}
     */
    public function uploadAvatar(string $userId, UploadedFile $avatarFile): array
    {
        // TODO: Implement avatar upload functionality
        throw new \Exception('Avatar upload not implemented yet');
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAvatar(string $userId): bool
    {
        // TODO: Implement avatar deletion functionality
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfileById(string $profileId): ?Profile
    {
        return $this->profileRepository->findById($profileId);
    }

    /**
     * {@inheritDoc}
     */
    public function updateVerificationStatus(string $userId, bool $isVerified, ?string $verificationStatus = null): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->profileRepository->update($profile->id, [
            'is_verified' => $isVerified,
            'verification_status' => $verificationStatus,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getVerificationStatus(string $userId): ?array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            return null;
        }

        return [
            'profile_verified' => $profile->is_verified,
            'verification_status' => $profile->verification_status,
            'submitted_at' => $profile->updated_at,
            'verified_at' => $profile->verified_at,
            'can_resubmit' => $profile->verification_status === 'rejected',
            'can_submit' => $profile->verification_status === 'pending',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function resubmitVerification(string $userId, array $data): ?array
    {
        // TODO: Implement verification resubmission functionality
        throw new \Exception('Verification resubmission not implemented yet');
    }
}
