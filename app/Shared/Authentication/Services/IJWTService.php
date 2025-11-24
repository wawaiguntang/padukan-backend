<?php

namespace App\Shared\Authentication\Services;

use Modules\Authentication\Models\User;

/**
 * Interface for JWT Service
 *
 * This interface defines the contract for JWT token operations
 * including generation, validation, and refresh token management.
 */
interface IJWTService
{
    /**
     * Validate and decode JWT access token
     *
     * @param string $token The JWT access token
     * @return array|null Returns decoded payload if valid, null if invalid
     */
    public function validateAccessToken(string $token): ?array;
}