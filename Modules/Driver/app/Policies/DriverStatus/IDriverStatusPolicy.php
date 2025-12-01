<?php

namespace Modules\Driver\Policies\DriverStatus;

interface IDriverStatusPolicy
{
    /**
     * Check if user can view their driver status
     */
    public function canViewStatus(string $userId, string $profileId): bool;

    /**
     * Check if user can update their online/offline status
     */
    public function canUpdateOnlineStatus(string $userId, string $profileId): bool;

    /**
     * Check if user can update their operational status
     */
    public function canUpdateOperationalStatus(string $userId, string $profileId, string $newStatus): bool;

    /**
     * Check if user can set active service
     */
    public function canSetActiveService(string $userId, string $profileId, string $service): bool;

    /**
     * Check if user can update location
     */
    public function canUpdateLocation(string $userId, string $profileId): bool;

    /**
     * Check if driver can go online (has verified vehicles, etc.)
     */
    public function canGoOnline(string $userId, string $profileId): bool;

    /**
     * Check if user can use a specific service based on their verified vehicles
     */
    public function canUseServiceWithVehicles(string $userId, string $profileId, string $service): bool;
}
