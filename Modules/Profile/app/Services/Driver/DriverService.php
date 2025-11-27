<?php

namespace Modules\Profile\Services\Driver;

use Modules\Profile\Repositories\Profile\IProfileRepository;
use Modules\Profile\Repositories\Driver\IDriverRepository;
use Modules\Profile\Services\FileUpload\IFileUploadService;
use Modules\Profile\Exceptions\ProfileNotFoundException;
use Modules\Profile\Exceptions\DriverProfileNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Driver Service Implementation
 *
 * Handles driver profile business logic
 */
class DriverService implements IDriverService
{
    private IProfileRepository $profileRepository;
    private IDriverRepository $driverRepository;
    private IFileUploadService $fileUploadService;

    public function __construct(
        IProfileRepository $profileRepository,
        IDriverRepository $driverRepository,
        IFileUploadService $fileUploadService
    ) {
        $this->profileRepository = $profileRepository;
        $this->driverRepository = $driverRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get driver profile by user ID
     */
    public function getDriverProfile(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
        ];
    }

    /**
     * Update driver profile
     */
    public function updateDriverProfile(string $userId, array $data): array
    {
        DB::beginTransaction();

        try {
            $profile = $this->profileRepository->findByUserId($userId);

            if (!$profile) {
                throw new ProfileNotFoundException($userId);
            }

            // Handle avatar upload if provided
            if (isset($data['avatar']) && $data['avatar']) {
                $avatarPath = $this->fileUploadService->uploadAvatar($data['avatar'], 'drivers');
                $data['avatar'] = $avatarPath;

                // Delete old avatar if exists
                if ($profile->avatar) {
                    $this->fileUploadService->deleteFile($profile->avatar);
                }
            }

            // Update profile
            $updatedProfile = $this->profileRepository->update($profile->id, $data);

            DB::commit();

            $driverProfile = $this->driverRepository->findDriverProfileByProfileId($updatedProfile->id);

            if (!$driverProfile) {
                throw new DriverProfileNotFoundException($userId);
            }

            return [
                'profile' => $updatedProfile,
                'driver_profile' => $driverProfile,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get driver vehicles
     */
    public function getDriverVehicles(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'vehicles' => $driverProfile->vehicles,
        ];
    }

    /**
     * Create driver vehicle
     */
    public function createDriverVehicle(string $userId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        $vehicle = $this->driverRepository->createVehicle($driverProfile->id, $data);

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'vehicle' => $vehicle,
        ];
    }

    /**
     * Update driver vehicle
     */
    public function updateDriverVehicle(string $userId, string $vehicleId, array $data): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        $vehicle = $this->driverRepository->updateVehicle($vehicleId, $data, $driverProfile->id);

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'vehicle' => $vehicle,
        ];
    }

    /**
     * Delete driver vehicle
     */
    public function deleteDriverVehicle(string $userId, string $vehicleId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return $this->driverRepository->deleteVehicle($vehicleId, $driverProfile->id);
    }

    /**
     * Get driver documents
     */
    public function getDriverDocuments(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'documents' => $driverProfile->documents,
        ];
    }

    /**
     * Create driver document
     */
    public function createDriverDocument(string $userId, array $data): array
    {
        DB::beginTransaction();

        try {
            $profile = $this->profileRepository->findByUserId($userId);

            if (!$profile) {
                throw new ProfileNotFoundException($userId);
            }

            $driverProfile = $profile->driverProfile;

            if (!$driverProfile) {
                throw new DriverProfileNotFoundException($userId);
            }

            // Handle file upload
            if (isset($data['file']) && $data['file']) {
                $uploadResult = $this->fileUploadService->uploadDocument($data['file'], $userId, 'driver_document');
                $data['file_path'] = $uploadResult['path'];
                $data['file_name'] = $uploadResult['file_name'];
                $data['mime_type'] = $uploadResult['mime_type'];
                $data['file_size'] = $uploadResult['file_size'];
                unset($data['file']);
            }

            $document = $this->driverRepository->createDocument($driverProfile->id, $data);

            DB::commit();

            return [
                'profile' => $profile,
                'driver_profile' => $driverProfile,
                'document' => $document,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update driver document
     */
    public function updateDriverDocument(string $userId, string $documentId, array $data): array
    {
        DB::beginTransaction();

        try {
            $profile = $this->profileRepository->findByUserId($userId);

            if (!$profile) {
                throw new ProfileNotFoundException($userId);
            }

            $driverProfile = $profile->driverProfile;

            if (!$driverProfile) {
                throw new DriverProfileNotFoundException($userId);
            }

            // Handle file upload if provided
            if (isset($data['file']) && $data['file']) {
                $uploadResult = $this->fileUploadService->uploadDocument($data['file'], $userId, 'driver_document');
                $data['file_path'] = $uploadResult['path'];
                $data['file_name'] = $uploadResult['file_name'];
                $data['mime_type'] = $uploadResult['mime_type'];
                $data['file_size'] = $uploadResult['file_size'];
                unset($data['file']);
            }

            $document = $this->driverRepository->updateDocument($documentId, $data, $driverProfile->id);

            DB::commit();

            return [
                'profile' => $profile,
                'driver_profile' => $driverProfile,
                'document' => $document,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete driver document
     */
    public function deleteDriverDocument(string $userId, string $documentId): bool
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return $this->driverRepository->deleteDocument($documentId, $driverProfile->id);
    }

    /**
     * Get driver document file URL
     */
    public function getDriverDocumentFileUrl(string $userId, string $documentId): string
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return $this->driverRepository->getDocumentFileUrl($documentId, $driverProfile->id);
    }

    /**
     * Request driver verification
     */
    public function requestDriverVerification(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        $updatedDriverProfile = $this->driverRepository->updateDriverProfile($driverProfile->id, [
            'verification_status' => 'pending'
        ]);

        return [
            'profile' => $profile,
            'driver_profile' => $updatedDriverProfile,
        ];
    }

    /**
     * Get driver verification status
     */
    public function getDriverVerificationStatus(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'verification_status' => $driverProfile->verification_status,
            'is_verified' => $driverProfile->is_verified,
        ];
    }

    /**
     * Get driver documents for verification status check
     */
    public function getDriverDocumentsForVerification(string $userId): array
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new ProfileNotFoundException($userId);
        }

        $driverProfile = $profile->driverProfile;

        if (!$driverProfile) {
            throw new DriverProfileNotFoundException($userId);
        }

        $documents = $driverProfile->documents;

        // Check if all required document types are present
        $requiredTypes = ['id_card', 'sim', 'stnk', 'vehicle_photo'];
        $submittedTypes = $documents->pluck('type')->toArray();

        $missingDocuments = array_diff($requiredTypes, $submittedTypes);

        return [
            'profile' => $profile,
            'driver_profile' => $driverProfile,
            'documents' => $documents,
            'required_types' => $requiredTypes,
            'submitted_types' => $submittedTypes,
            'missing_documents' => array_values($missingDocuments),
            'is_complete' => empty($missingDocuments),
        ];
    }
}