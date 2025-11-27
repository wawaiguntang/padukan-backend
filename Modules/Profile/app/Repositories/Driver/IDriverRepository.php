<?php

namespace Modules\Profile\Repositories\Driver;

use Modules\Profile\Models\DriverProfile;
use Modules\Profile\Models\DriverVehicle;
use Modules\Profile\Models\DriverDocument;

/**
 * Driver Repository Interface
 *
 * Defines methods for driver data access
 */
interface IDriverRepository
{
    /**
     * Find driver profile by profile ID
     *
     * @param string $profileId
     * @return DriverProfile|null
     */
    public function findDriverProfileByProfileId(string $profileId): ?DriverProfile;

    /**
     * Create driver profile
     *
     * @param array $data
     * @return DriverProfile
     */
    public function createDriverProfile(array $data): DriverProfile;

    /**
     * Update driver profile
     *
     * @param string $driverProfileId
     * @param array $data
     * @return DriverProfile
     */
    public function updateDriverProfile(string $driverProfileId, array $data): DriverProfile;

    /**
     * Create driver vehicle
     *
     * @param string $driverProfileId
     * @param array $data
     * @return DriverVehicle
     */
    public function createVehicle(string $driverProfileId, array $data): DriverVehicle;

    /**
     * Find vehicle by ID and driver profile ID
     *
     * @param string $vehicleId
     * @param string $driverProfileId
     * @return DriverVehicle|null
     */
    public function findVehicleByIdAndDriverProfileId(string $vehicleId, string $driverProfileId): ?DriverVehicle;

    /**
     * Update driver vehicle
     *
     * @param string $vehicleId
     * @param array $data
     * @param string $driverProfileId
     * @return DriverVehicle
     */
    public function updateVehicle(string $vehicleId, array $data, string $driverProfileId): DriverVehicle;

    /**
     * Delete driver vehicle
     *
     * @param string $vehicleId
     * @param string $driverProfileId
     * @return bool
     */
    public function deleteVehicle(string $vehicleId, string $driverProfileId): bool;

    /**
     * Get vehicles by driver profile ID
     *
     * @param string $driverProfileId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByDriverProfileId(string $driverProfileId);

    /**
     * Create driver document
     *
     * @param string $driverProfileId
     * @param array $data
     * @return DriverDocument
     */
    public function createDocument(string $driverProfileId, array $data): DriverDocument;

    /**
     * Find document by ID and driver profile ID
     *
     * @param string $documentId
     * @param string $driverProfileId
     * @return DriverDocument|null
     */
    public function findDocumentByIdAndDriverProfileId(string $documentId, string $driverProfileId): ?DriverDocument;

    /**
     * Update driver document
     *
     * @param string $documentId
     * @param array $data
     * @param string $driverProfileId
     * @return DriverDocument
     */
    public function updateDocument(string $documentId, array $data, string $driverProfileId): DriverDocument;

    /**
     * Delete driver document
     *
     * @param string $documentId
     * @param string $driverProfileId
     * @return bool
     */
    public function deleteDocument(string $documentId, string $driverProfileId): bool;

    /**
     * Get documents by driver profile ID
     *
     * @param string $driverProfileId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDocumentsByDriverProfileId(string $driverProfileId);

    /**
     * Get document file URL
     *
     * @param string $documentId
     * @param string $driverProfileId
     * @return string
     */
    public function getDocumentFileUrl(string $documentId, string $driverProfileId): string;
}