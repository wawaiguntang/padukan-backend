<?php

namespace Modules\Profile\Repositories\Driver;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Modules\Profile\Models\DriverProfile;
use Modules\Profile\Models\DriverVehicle;
use Modules\Profile\Models\DriverDocument;
use Modules\Profile\Services\FileUpload\IFileUploadService;
use Modules\Profile\Cache\KeyManager\IKeyManager;
use Illuminate\Support\Facades\Storage;

/**
 * Driver Repository Implementation
 *
 * Handles driver data access operations with caching
 */
class DriverRepository implements IDriverRepository
{
    private IFileUploadService $fileUploadService;
    private IKeyManager $keyManager;
    private CacheContract $cache;
    private int $cacheTtl = 900; // 15 minutes

    public function __construct(IFileUploadService $fileUploadService, IKeyManager $keyManager, CacheContract $cache)
    {
        $this->fileUploadService = $fileUploadService;
        $this->keyManager = $keyManager;
        $this->cache = $cache;
    }

    /**
     * Find driver profile by profile ID
     */
    public function findDriverProfileByProfileId(string $profileId): ?DriverProfile
    {
        $cacheKey = $this->keyManager::driverProfileByProfileId($profileId);

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($profileId) {
            return DriverProfile::where('profile_id', $profileId)->first();
        });
    }

    /**
     * Create driver profile
     */
    public function createDriverProfile(array $data): DriverProfile
    {
        return DriverProfile::create($data);
    }

    /**
     * Update driver profile
     */
    public function updateDriverProfile(string $driverProfileId, array $data): DriverProfile
    {
        $driverProfile = DriverProfile::findOrFail($driverProfileId);
        $driverProfile->update($data);
        $updatedProfile = $driverProfile->fresh();

        // Invalidate cache
        $this->cache->forget($this->keyManager::driverProfileByProfileId($updatedProfile->profile_id));

        return $updatedProfile;
    }

    /**
     * Create driver vehicle
     */
    public function createVehicle(string $driverProfileId, array $data): DriverVehicle
    {
        $data['driver_profile_id'] = $driverProfileId;
        return DriverVehicle::create($data);
    }

    /**
     * Find vehicle by ID and driver profile ID
     */
    public function findVehicleByIdAndDriverProfileId(string $vehicleId, string $driverProfileId): ?DriverVehicle
    {
        return DriverVehicle::where('id', $vehicleId)
            ->where('driver_profile_id', $driverProfileId)
            ->first();
    }

    /**
     * Update driver vehicle
     */
    public function updateVehicle(string $vehicleId, array $data, string $driverProfileId): DriverVehicle
    {
        $vehicle = $this->findVehicleByIdAndDriverProfileId($vehicleId, $driverProfileId);

        if (!$vehicle) {
            throw new \Exception('Vehicle not found');
        }

        $vehicle->update($data);
        return $vehicle->fresh();
    }

    /**
     * Delete driver vehicle
     */
    public function deleteVehicle(string $vehicleId, string $driverProfileId): bool
    {
        $vehicle = $this->findVehicleByIdAndDriverProfileId($vehicleId, $driverProfileId);

        if (!$vehicle) {
            return false;
        }

        return $vehicle->delete();
    }

    /**
     * Get vehicles by driver profile ID
     */
    public function getVehiclesByDriverProfileId(string $driverProfileId)
    {
        return DriverVehicle::where('driver_profile_id', $driverProfileId)->get();
    }

    /**
     * Create driver document
     */
    public function createDocument(string $driverProfileId, array $data): DriverDocument
    {
        $data['driver_profile_id'] = $driverProfileId;
        return DriverDocument::create($data);
    }

    /**
     * Find document by ID and driver profile ID
     */
    public function findDocumentByIdAndDriverProfileId(string $documentId, string $driverProfileId): ?DriverDocument
    {
        return DriverDocument::where('id', $documentId)
            ->where('driver_profile_id', $driverProfileId)
            ->first();
    }

    /**
     * Update driver document
     */
    public function updateDocument(string $documentId, array $data, string $driverProfileId): DriverDocument
    {
        $document = $this->findDocumentByIdAndDriverProfileId($documentId, $driverProfileId);

        if (!$document) {
            throw new \Exception('Document not found');
        }

        // Delete old file if new file is uploaded
        if (isset($data['file_path']) && $document->file_path && $document->file_path !== $data['file_path']) {
            $this->fileUploadService->deleteFile($document->file_path);
        }

        $document->update($data);
        return $document->fresh();
    }

    /**
     * Delete driver document
     */
    public function deleteDocument(string $documentId, string $driverProfileId): bool
    {
        $document = $this->findDocumentByIdAndDriverProfileId($documentId, $driverProfileId);

        if (!$document) {
            return false;
        }

        // Delete file from storage
        if ($document->file_path) {
            $this->fileUploadService->deleteFile($document->file_path);
        }

        return $document->delete();
    }

    /**
     * Get documents by driver profile ID
     */
    public function getDocumentsByDriverProfileId(string $driverProfileId)
    {
        return DriverDocument::where('driver_profile_id', $driverProfileId)->get();
    }

    /**
     * Get document file URL
     */
    public function getDocumentFileUrl(string $documentId, string $driverProfileId): string
    {
        $document = $this->findDocumentByIdAndDriverProfileId($documentId, $driverProfileId);

        if (!$document) {
            throw new \Exception('Document not found');
        }

        return $this->fileUploadService->getFileUrl($document->file_path);
    }
}