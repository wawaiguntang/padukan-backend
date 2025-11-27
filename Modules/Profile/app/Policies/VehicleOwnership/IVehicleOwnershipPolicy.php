<?php

namespace Modules\Profile\Policies\BusinessValidation;

interface IVehicleOwnershipPolicy
{
    /**
     * Check if driver can add more vehicles
     */
    public function canAddVehicle(string $driverProfileId): bool;

    /**
     * Get maximum vehicles per driver
     */
    public function getMaxVehiclesPerDriver(): int;

    /**
     * Check if vehicle type is allowed
     */
    public function isVehicleTypeAllowed(string $vehicleType): bool;

    /**
     * Get allowed vehicle types
     */
    public function getAllowedVehicleTypes(): array;

    /**
     * Check if verification is required for vehicles
     */
    public function isVerificationRequired(): bool;

    /**
     * Check if owned vehicles should be auto-verified
     */
    public function shouldAutoVerifyOwned(): bool;
}