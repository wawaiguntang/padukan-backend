<?php

namespace Modules\Authentication\Services\JWT;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\User\IUserRepository;
use Modules\Authentication\Cache\KeyManager\IKeyManager;
use App\Shared\Authentication\Services\IJWTService as SharedIJWTService;

/**
 * JWT Service Implementation
 *
 * This class handles JWT token generation, validation, and refresh token management
 * with caching for optimal performance.
 */
class JWTService implements IJWTService, SharedIJWTService
{
    /**
     * The cache repository instance
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * The cache key manager instance
     *
     * @var IKeyManager
     */
    protected IKeyManager $cacheKeyManager;

    /**
     * The user repository instance
     *
     * @var IUserRepository
     */
    protected IUserRepository $userRepository;

    /**
     * JWT secret key
     *
     * @var string
     */
    protected string $secretKey;

    /**
     * Access token expiration time in minutes
     *
     * @var int
     */
    protected int $accessTokenExpiration = 15; // 15 minutes

    /**
     * Refresh token expiration time in days
     *
     * @var int
     */
    protected int $refreshTokenExpiration = 30; // 30 days

    /**
     * Constructor
     *
     * @param Cache $cache The cache repository instance
     * @param IKeyManager $cacheKeyManager The cache key manager instance
     * @param IUserRepository $userRepository The user repository instance
     */
    public function __construct(Cache $cache, IKeyManager $cacheKeyManager, IUserRepository $userRepository)
    {
        $this->cache = $cache;
        $this->cacheKeyManager = $cacheKeyManager;
        $this->userRepository = $userRepository;
        $this->secretKey = config('app.key', 'default-secret-key');
    }

    /**
     * {@inheritDoc}
     */
    public function generateTokens(User $user): array
    {
        // Generate access token
        $accessToken = $this->generateAccessToken($user);

        // Generate refresh token
        $refreshToken = $this->generateRefreshToken();

        // Cache refresh token with user association
        $this->storeRefreshToken($refreshToken, $user->id);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $this->accessTokenExpiration * 60, // Convert to seconds
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function validateAccessToken(string $token): ?array
    {
        try {
            $payload = $this->decodeToken($token);

            if (!$payload) {
                return null;
            }

            // Check if token is expired
            if (Carbon::createFromTimestamp($payload['exp'])->isPast()) {
                return null;
            }

            // Check if user still exists and active
            $user = $this->userRepository->findById($payload['sub']);
            if (!$user) {
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function refreshAccessToken(string $refreshToken): ?array
    {
        // Validate refresh token
        $userId = $this->getUserIdFromRefreshToken($refreshToken);

        if (!$userId) {
            return null;
        }

        // Get user
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return null;
        }

        // Generate new tokens
        return $this->generateTokens($user);
    }

    /**
     * {@inheritDoc}
     */
    public function invalidateRefreshToken(string $refreshToken): bool
    {
        $cacheKey = $this->cacheKeyManager::refreshToken($refreshToken);
        return $this->cache->forget($cacheKey);
    }

    /**
     * {@inheritDoc}
     */
    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateAccessToken($token);

        if (!$payload) {
            return null;
        }

        return $this->userRepository->findById($payload['sub']);
    }

    /**
     * Generate JWT access token
     *
     * @param User $user The user
     * @return string The JWT token
     */
    protected function generateAccessToken(User $user): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $now = Carbon::now();
        $payload = json_encode([
            'iss' => config('app.url', 'localhost'),
            'sub' => $user->id,
            'iat' => $now->timestamp,
            'exp' => $now->addMinutes($this->accessTokenExpiration)->timestamp,
            'jti' => Str::random(16), // Add unique JWT ID to ensure uniqueness
            'user' => [
                'id' => $user->id,
                'phone' => $user->phone,
                'email' => $user->email,
                'status' => $user->status->value,
            ],
        ]);
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->secretKey, true);
        $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Generate refresh token
     *
     * @return string The refresh token
     */
    protected function generateRefreshToken(): string
    {
        return Str::random(64);
    }

    /**
     * Decode JWT token
     *
     * @param string $token The JWT token
     * @return array|null The decoded payload
     */
    protected function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->secretKey, true);
        $expectedSignatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));

        if (!hash_equals($signatureEncoded, $expectedSignatureEncoded)) {
            return null;
        }

        // Decode payload
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadEncoded)), true);

        return $payload;
    }

    /**
     * Store refresh token in cache
     *
     * @param string $refreshToken The refresh token
     * @param string $userId The user ID
     * @return void
     */
    protected function storeRefreshToken(string $refreshToken, string $userId): void
    {
        $cacheKey = $this->cacheKeyManager::refreshToken($refreshToken);
        $this->cache->put($cacheKey, $userId, Carbon::now()->addDays($this->refreshTokenExpiration));
    }

    /**
     * Get user ID from refresh token
     *
     * @param string $refreshToken The refresh token
     * @return string|null The user ID if valid, null otherwise
     */
    protected function getUserIdFromRefreshToken(string $refreshToken): ?string
    {
        $cacheKey = $this->cacheKeyManager::refreshToken($refreshToken);
        return $this->cache->get($cacheKey);
    }
}