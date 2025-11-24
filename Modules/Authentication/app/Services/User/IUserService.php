<?php

namespace Modules\Authentication\Services\User;

use Modules\Authentication\Models\User;

/**
 * Interface for User Service
 *
 * This interface defines the contract for user authentication operations
 * including registration, login, and user management.
 */
interface IUserService
{
    /**
     * Register a new user with phone and/or email
     *
     * Creates a new user account and sends OTP for verification.
     * At least one identifier (phone or email) must be provided.
     *
     * @param string|null $phone User's phone number (optional)
     * @param string|null $email User's email address (optional)
     * @param string $password User's password (will be hashed)
     * @return User The created user model
     * @throws \Modules\Authentication\Exceptions\UserAlreadyExistsException
     */
    public function register(?string $phone, ?string $email, string $password): User;

    /**
     * Authenticate user login
     *
     * Validates user credentials using phone or email identifier and returns JWT tokens.
     *
     * @param string $identifier User's phone number or email address
     * @param string $password User's password
     * @return array Returns array with:
     * - user: User - The authenticated user model
     * - access_token: string - JWT access token
     * - refresh_token: string - Refresh token for getting new access token
     * - token_type: string - Token type (usually "Bearer")
     * - expires_in: int - Access token expiration time in seconds
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     * @throws \Modules\Authentication\Exceptions\InvalidCredentialsException
     */
    public function login(string $identifier, string $password): array;

    /**
     * Get user by ID
     *
     * @param string $id The user's UUID
     * @return User The user model
     * @throws \Modules\Authentication\Exceptions\UserNotFoundException
     */
    public function getUserById(string $id): User;

    /**
     * Logout user by invalidating refresh token
     *
     * @param string $refreshToken The refresh token to invalidate
     * @return bool True if logout successful
     */
    public function logout(string $refreshToken): bool;

    /**
     * Refresh access token using refresh token
     *
     * @param string $refreshToken The refresh token
     * @return array|null Returns new tokens array if refresh successful, null if invalid
     */
    public function refreshToken(string $refreshToken): ?array;
}