<?php

namespace Modules\Authentication\Services\JWT;

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
     * Generate access and refresh tokens for a user
     *
     * @param User $user The authenticated user
     * @return array Returns array with:
     * - access_token: string - JWT access token
     * - refresh_token: string - Refresh token for getting new access token
     * - token_type: string - Token type (usually "Bearer")
     * - expires_in: int - Access token expiration time in seconds
     */
    public function generateTokens(User $user): array;

    /**
     * Validate and decode JWT access token
     *
     * @param string $token The JWT access token
     * @return array|null Returns decoded payload if valid, null if invalid
     */
    public function validateAccessToken(string $token): ?array;

    /**
     * Refresh access token using refresh token
     *
     * @param string $refreshToken The refresh token
     * @return array|null Returns new tokens array if refresh successful, null if invalid
     */
    public function refreshAccessToken(string $refreshToken): ?array;

    /**
     * Invalidate refresh token (logout)
     *
     * @param string $refreshToken The refresh token to invalidate
     * @return bool True if invalidated successfully
     */
    public function invalidateRefreshToken(string $refreshToken): bool;

    /**
     * Get user from JWT token
     *
     * @param string $token The JWT access token
     * @return User|null The user if token is valid, null otherwise
     */
    public function getUserFromToken(string $token): ?User;
}