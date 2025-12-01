<?php

namespace Modules\Driver\Policies\ServiceValidation;

interface IServiceValidationPolicy
{
    /**
     * Check if driver can provide a specific service based on verified vehicles
     */
    public function canProvideService(string $userId, string $profileId, string $service): bool;

    /**
     * Get all services a driver can provide
     */
    public function getAvailableServices(string $userId, string $profileId): array;

    /**
     * Validate service assignment for driver
     */
    public function validateServiceAssignment(string $userId, string $profileId, string $service): bool;

    /**
     * Check if driver meets service requirements
     */
    public function meetsServiceRequirements(string $userId, string $profileId, string $service): bool;
}
