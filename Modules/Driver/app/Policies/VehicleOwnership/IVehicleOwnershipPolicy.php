<?php

namespace Modules\Driver\Policies\VehicleOwnership;

interface IVehicleOwnershipPolicy
{
    /**
     * Check if user owns the vehicle
     */
    public function ownsVehicle(string $userId, string $vehicleId): bool;

    /**
     * Check if user can access vehicle data
     */
    public function canAccessVehicle(string $userId, string $vehicleId): bool;

    /**
     * Check if user can modify vehicle data
     */
    public function canModifyVehicle(string $userId, string $vehicleId): bool;

    /**
     * Check if user can delete vehicle
     */
    public function canDeleteVehicle(string $userId, string $vehicleId): bool;

    /**
     * Check if user can register new vehicle
     */
    public function canRegisterVehicle(string $userId): bool;

    /**
     * Check if user can submit vehicle verification
     */
    public function canSubmitVehicleVerification(string $userId, string $vehicleId): bool;

    /**
     * Check if user can resubmit vehicle verification
     */
    public function canResubmitVehicleVerification(string $userId, string $vehicleId): bool;

    /**
     * Check if user can register vehicle of specific type
     */
    public function canRegisterVehicleType(string $userId, string $vehicleType): bool;

    /**
     * Get max vehicles per driver from policy settings
     */
    public function getMaxVehiclesPerDriver(): int;
}
