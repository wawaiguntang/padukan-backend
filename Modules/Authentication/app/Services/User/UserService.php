<?php

namespace Modules\Authentication\Services\User;

use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Exceptions\InvalidCredentialsException;
use Modules\Authentication\Exceptions\UserAlreadyExistsException;
use Modules\Authentication\Exceptions\UserNotFoundException;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\User\IUserRepository;
use Modules\Authentication\Services\JWT\IJWTService;
use Modules\Authentication\Services\Verification\IVerificationService;

/**
 * User Service Implementation
 *
 * This class handles user authentication operations including
 * registration, login, and user management with proper validation.
 */
class UserService implements IUserService
{
    /**
     * The user repository instance
     *
     * @var IUserRepository
     */
    protected IUserRepository $userRepository;

    /**
     * The verification service instance
     *
     * @var IVerificationService
     */
    protected IVerificationService $verificationService;

    /**
     * The JWT service instance
     *
     * @var IJWTService
     */
    protected IJWTService $jwtService;

    /**
     * Constructor
     *
     * @param IUserRepository $userRepository The user repository instance
     * @param IVerificationService $verificationService The verification service instance
     * @param IJWTService $jwtService The JWT service instance
     */
    public function __construct(IUserRepository $userRepository, IVerificationService $verificationService, IJWTService $jwtService)
    {
        $this->userRepository = $userRepository;
        $this->verificationService = $verificationService;
        $this->jwtService = $jwtService;
    }

    /**
     * {@inheritDoc}
     */
    public function register(?string $phone, ?string $email, string $password): User
    {
        // Validate that at least one identifier is provided
        if (!$phone && !$email) {
            throw new \InvalidArgumentException('Either phone or email must be provided');
        }

        // Check if user already exists
        if ($phone && $this->userRepository->existsByIdentifier($phone)) {
            throw new UserAlreadyExistsException('auth.user.phone_already_exists', ['phone' => $phone]);
        }

        if ($email && $this->userRepository->existsByIdentifier($email)) {
            throw new UserAlreadyExistsException('auth.user.email_already_exists', ['email' => $email]);
        }

        // Create user
        $user = $this->userRepository->create([
            'phone' => $phone,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatus::PENDING,
        ]);

        // Send OTP for verification
        $identifierType = $phone ? IdentifierType::PHONE : IdentifierType::EMAIL;
        $identifier = $phone ?: $email;

        try {
            $this->verificationService->sendOtp($user->id, $identifierType);
        } catch (\Exception $e) {
            // If OTP sending fails, we still return the user
            // The user can request OTP resend later
            // Log the error for monitoring
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function login(string $identifier, string $password): array
    {
        // Find user by identifier
        $user = $this->userRepository->findByIdentifier($identifier);

        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['identifier' => $identifier]);
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException('auth.invalid_credentials');
        }

        // Generate JWT tokens
        $tokens = $this->jwtService->generateTokens($user);

        return array_merge($tokens, ['user' => $user]);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserById(string $id): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new UserNotFoundException('auth.user.not_found', ['id' => $id]);
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function logout(string $refreshToken): bool
    {
        return $this->jwtService->invalidateRefreshToken($refreshToken);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshToken(string $refreshToken): ?array
    {
        return $this->jwtService->refreshAccessToken($refreshToken);
    }
}