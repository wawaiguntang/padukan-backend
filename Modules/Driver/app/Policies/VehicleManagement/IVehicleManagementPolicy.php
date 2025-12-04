<?php

namespace Modules\Driver\Policies\VehicleManagement;

interface IVehicleManagementPolicy
{
    /**
     * Check if user owns the vehicle
     */
    public function ownsVehicle(string $userId, string $vehicleId): bool;

    /**
     * Check if user can register new vehicle
     */
    public function canRegisterVehicle(string $userId): bool;

    /**
     * Check if user can register vehicle of specific type
     */
    public function canRegisterVehicleType(string $userId, string $vehicleType): bool;

    /**
     * Get maximum vehicles allowed per driver
     */
    public function getMaxVehiclesPerDriver(): int;
}
