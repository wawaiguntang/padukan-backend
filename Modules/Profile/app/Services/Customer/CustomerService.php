<?php

namespace Modules\Profile\Services\Customer;

use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Profile\Policies\ProfileOwnership\IProfileOwnershipPolicy;
use Modules\Profile\Services\FileUpload\IFileUploadService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\UnauthorizedAccessException;

class CustomerService implements ICustomerService
{
    private IProfileRepository $profileRepository;
    private IProfileOwnershipPolicy $ownershipPolicy;
    private IFileUploadService $fileUploadService;

    public function __construct(
        IProfileRepository $profileRepository,
        IProfileOwnershipPolicy $ownershipPolicy,
        IFileUploadService $fileUploadService
    ) {
        $this->profileRepository = $profileRepository;
        $this->ownershipPolicy = $ownershipPolicy;
        $this->fileUploadService = $fileUploadService;
    }

    public function getProfile(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException('profile_not_found');
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        $customerProfile = $profile->customerProfile;

        return [
            'profile' => $profile,
            'customer_profile' => $customerProfile,
        ];
    }

    public function updateProfile(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException('profile_not_found');
        }

        if (!$this->ownershipPolicy->canAccessProfile($userId, $profile->id)) {
            throw new UnauthorizedAccessException();
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $avatarData = $this->fileUploadService->uploadAvatar($data['avatar'], $userId);

            // Delete old avatar if exists
            if ($profile->avatar && $this->fileUploadService->deleteFile($profile->avatar)) {
                // Old avatar deleted
            }

            $data['avatar'] = $avatarData['path'];
        } else {
            // Remove avatar from data if not provided
            unset($data['avatar']);
        }

        $success = $this->profileRepository->update($profile->id, $data);

        if (!$success) {
            throw new \Exception(__('profile::validation.update_failed'));
        }

        $updatedProfile = $this->profileRepository->findById($profile->id);
        $customerProfile = $updatedProfile->customerProfile;

        return [
            'profile' => $updatedProfile,
            'customer_profile' => $customerProfile,
        ];
    }
}