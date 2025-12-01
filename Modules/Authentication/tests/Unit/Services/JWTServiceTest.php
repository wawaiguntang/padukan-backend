<?php

namespace Modules\Authentication\Tests\Unit\Services;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Authentication\Database\Factories\UserFactory;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\User\IUserRepository;
use Modules\Authentication\Cache\KeyManager\IKeyManager;
use Modules\Authentication\Services\JWT\JWTService;
use Tests\TestCase;

/**
 * JWT Service Test
 *
 * This test class covers all JWT service functionality
 * including token generation, validation, and refresh operations.
 */
class JWTServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The JWT service instance
     *
     * @var JWTService
     */
    protected JWTService $jwtService;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userRepository = app(IUserRepository::class);
        $keyManager = app(IKeyManager::class);
        $this->jwtService = new JWTService(Cache::store(), $keyManager, $userRepository);
    }

    /**
     * Test generating tokens for a user
     *
     * @return void
     */
    public function test_generate_tokens()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Act
        $tokens = $this->jwtService->generateTokens($user);

        // Assert
        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertArrayHasKey('refresh_token', $tokens);
        $this->assertArrayHasKey('token_type', $tokens);
        $this->assertArrayHasKey('expires_in', $tokens);
        $this->assertEquals('Bearer', $tokens['token_type']);
        $this->assertIsInt($tokens['expires_in']);
        $this->assertGreaterThan(0, $tokens['expires_in']);
    }

    /**
     * Test validating a valid access token
     *
     * @return void
     */
    public function test_validate_access_token_valid()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $tokens = $this->jwtService->generateTokens($user);

        // Act
        $payload = $this->jwtService->validateAccessToken($tokens['access_token']);

        // Assert
        $this->assertNotNull($payload);
        $this->assertEquals($user->id, $payload['sub']);
        $this->assertArrayHasKey('user', $payload);
        $this->assertEquals($user->id, $payload['user']['id']);
    }

    /**
     * Test validating an invalid access token
     *
     * @return void
     */
    public function test_validate_access_token_invalid()
    {
        // Act
        $payload = $this->jwtService->validateAccessToken('invalid-token');

        // Assert
        $this->assertNull($payload);
    }

    /**
     * Test validating an expired access token
     *
     * @return void
     */
    public function test_validate_access_token_expired()
    {
        // Arrange - Create an expired token manually
        $user = UserFactory::new()->create();
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $payload = json_encode([
            'iss' => config('app.url', 'localhost'),
            'sub' => $user->id,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->subMinutes(1)->timestamp, // Already expired
            'user' => [
                'id' => $user->id,
                'phone' => $user->phone,
                'email' => $user->email,
                'status' => $user->status->value,
            ],
        ]);
        $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $secretKey = config('app.key', 'default-secret-key');
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $secretKey, true);
        $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $expiredToken = $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;

        // Act
        $payload = $this->jwtService->validateAccessToken($expiredToken);

        // Assert
        $this->assertNull($payload);
    }

    /**
     * Test refreshing access token with valid refresh token
     *
     * @return void
     */
    public function test_refresh_access_token_valid()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $tokens = $this->jwtService->generateTokens($user);

        // Act
        $newTokens = $this->jwtService->refreshAccessToken($tokens['refresh_token']);

        // Assert
        $this->assertNotNull($newTokens);
        $this->assertArrayHasKey('access_token', $newTokens);
        $this->assertArrayHasKey('refresh_token', $newTokens);
        $this->assertNotEquals($tokens['access_token'], $newTokens['access_token']);
        $this->assertNotEquals($tokens['refresh_token'], $newTokens['refresh_token']);
    }

    /**
     * Test refreshing access token with invalid refresh token
     *
     * @return void
     */
    public function test_refresh_access_token_invalid()
    {
        // Act
        $newTokens = $this->jwtService->refreshAccessToken('invalid-refresh-token');

        // Assert
        $this->assertNull($newTokens);
    }

    /**
     * Test invalidating refresh token
     *
     * @return void
     */
    public function test_invalidate_refresh_token()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $tokens = $this->jwtService->generateTokens($user);

        // Act
        $result = $this->jwtService->invalidateRefreshToken($tokens['refresh_token']);

        // Assert
        $this->assertTrue($result);

        // Verify refresh token is invalidated
        $newTokens = $this->jwtService->refreshAccessToken($tokens['refresh_token']);
        $this->assertNull($newTokens);
    }

    /**
     * Test getting user from valid token
     *
     * @return void
     */
    public function test_get_user_from_token_valid()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $tokens = $this->jwtService->generateTokens($user);

        // Act
        $tokenUser = $this->jwtService->getUserFromToken($tokens['access_token']);

        // Assert
        $this->assertNotNull($tokenUser);
        $this->assertEquals($user->id, $tokenUser->id);
    }

    /**
     * Test getting user from invalid token
     *
     * @return void
     */
    public function test_get_user_from_token_invalid()
    {
        // Act
        $tokenUser = $this->jwtService->getUserFromToken('invalid-token');

        // Assert
        $this->assertNull($tokenUser);
    }

    /**
     * Test getting user from token when user no longer exists
     *
     * @return void
     */
    public function test_get_user_from_token_user_deleted()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $tokens = $this->jwtService->generateTokens($user);
        $user->delete(); // Delete user

        // Act
        $tokenUser = $this->jwtService->getUserFromToken($tokens['access_token']);

        // Assert
        $this->assertNull($tokenUser);
    }
}
