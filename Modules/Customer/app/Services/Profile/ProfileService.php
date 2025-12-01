<?php

namespace Modules\Customer\Services\Profile;

use Modules\Customer\Enums\GenderEnum;
use Modules\Customer\Models\Profile;
use Modules\Customer\Repositories\Profile\IProfileRepository;
use Modules\Customer\Services\FileUpload\IFileUploadService;
use Modules\Customer\Exceptions\ProfileNotFoundException;
use Modules\Customer\Exceptions\ProfileAlreadyExistsException;
use Modules\Customer\Exceptions\FileUploadException;
use Modules\Customer\Exceptions\FileValidationException;
use Illuminate\Http\UploadedFile;

/**
 * Profile Service Implementation
 *
 * This class handles profile business logic operations
 * for the customer module.
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
     * The file upload service instance
     *
     * @var IFileUploadService
     */
    protected IFileUploadService $fileUploadService;

    /**
     * Constructor
     *
     * @param IProfileRepository $profileRepository The profile repository instance
     * @param IFileUploadService $fileUploadService The file upload service instance
     */
    public function __construct(IProfileRepository $profileRepository, IFileUploadService $fileUploadService)
    {
        $this->profileRepository = $profileRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * {@inheritDoc}
     */
    public function createProfile(string $userId, array $data): Profile
    {
        // Check if profile already exists
        if ($this->profileRepository->existsByUserId($userId)) {
            throw new ProfileAlreadyExistsException();
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
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException();
        }

        try {
            // Delete existing avatar if it exists
            if ($profile->avatar) {
                $this->fileUploadService->deleteAvatar($profile->avatar);
            }

            // Upload new avatar
            $uploadResult = $this->fileUploadService->uploadAvatar($avatarFile, $userId);

            // Update profile with new avatar path
            $this->profileRepository->update($profile->id, ['avatar' => $uploadResult['path']]);

            return $uploadResult;
        } catch (\Exception $e) {
            // Re-throw validation exceptions as-is, wrap others in FileUploadException
            if ($e instanceof FileValidationException) {
                throw $e;
            }
            throw new FileUploadException('customer.file.upload_failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAvatar(string $userId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile || !$profile->avatar) {
            return false;
        }

        // Delete the file
        $fileDeleted = $this->fileUploadService->deleteAvatar($profile->avatar);

        if ($fileDeleted) {
            // Update profile to remove avatar reference
            $this->profileRepository->update($profile->id, ['avatar' => null]);
        }

        return $fileDeleted;
    }
}
