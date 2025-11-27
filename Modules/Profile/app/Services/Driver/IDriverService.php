<?php

namespace Modules\Profile\Services\Driver;

/**
 * Driver Service Interface
 *
 * Defines methods for driver profile management
 */
interface IDriverService
{
    /**
     * Get driver profile by user ID
     *
     * @param string $userId
     * @return array
     */
    public function getDriverProfile(string $userId): array;

    /**
     * Update driver profile
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function updateDriverProfile(string $userId, array $data): array;

    /**
     * Get driver vehicles
     *
     * @param string $userId
     * @return array
     */
    public function getDriverVehicles(string $userId): array;

    /**
     * Create driver vehicle
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createDriverVehicle(string $userId, array $data): array;

    /**
     * Update driver vehicle
     *
     * @param string $userId
     * @param string $vehicleId
     * @param array $data
     * @return array
     */
    public function updateDriverVehicle(string $userId, string $vehicleId, array $data): array;

    /**
     * Delete driver vehicle
     *
     * @param string $userId
     * @param string $vehicleId
     * @return bool
     */
    public function deleteDriverVehicle(string $userId, string $vehicleId): bool;

    /**
     * Get driver documents
     *
     * @param string $userId
     * @return array
     */
    public function getDriverDocuments(string $userId): array;

    /**
     * Create driver document
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createDriverDocument(string $userId, array $data): array;

    /**
     * Update driver document
     *
     * @param string $userId
     * @param string $documentId
     * @param array $data
     * @return array
     */
    public function updateDriverDocument(string $userId, string $documentId, array $data): array;

    /**
     * Delete driver document
     *
     * @param string $userId
     * @param string $documentId
     * @return bool
     */
    public function deleteDriverDocument(string $userId, string $documentId): bool;

    /**
     * Get driver document file URL
     *
     * @param string $userId
     * @param string $documentId
     * @return string
     */
    public function getDriverDocumentFileUrl(string $userId, string $documentId): string;

    /**
     * Request driver verification
     *
     * @param string $userId
     * @return array
     */
    public function requestDriverVerification(string $userId): array;

    /**
     * Get driver verification status
     *
     * @param string $userId
     * @return array
     */
    public function getDriverVerificationStatus(string $userId): array;

    /**
     * Get driver documents (for verification status check)
     *
     * @param string $userId
     * @return array
     */
    public function getDriverDocumentsForVerification(string $userId): array;
}