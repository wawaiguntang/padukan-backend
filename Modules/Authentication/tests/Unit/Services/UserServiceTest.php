<?php

namespace Modules\Authentication\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\User\IUserRepository;
use Modules\Authentication\Services\JWT\IJWTService;
use Modules\Authentication\Services\User\UserService;
use Tests\TestCase;

/**
 * User Service Test
 *
 * Tests all UserService functionality including registration, login, and token management
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The UserService instance
     *
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Mocked dependencies
     */
    protected $userRepositoryMock;
    protected $verificationServiceMock;
    protected $jwtServiceMock;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks using Mockery
        $this->userRepositoryMock = \Mockery::mock(IUserRepository::class);
        $this->verificationServiceMock = \Mockery::mock(\Modules\Authentication\Services\Verification\IVerificationService::class);
        $this->jwtServiceMock = \Mockery::mock(IJWTService::class);

        // Create service instance with mocks
        $this->userService = new \Modules\Authentication\Services\User\UserService(
            $this->userRepositoryMock,
            $this->verificationServiceMock,
            $this->jwtServiceMock
        );
    }

    /**
     * Tear down the test environment
     *
     * @return void
     */
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful user registration
     *
     * @return void
     */
    public function test_register_success()
    {
        $userData = [
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ];

        $expectedUser = new User([
            'id' => 'uuid-123',
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::PENDING,
        ]);
        $expectedUser->id = 'uuid-123'; // Ensure ID is set

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with('+6281234567890')
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with('test@example.com')
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($data) {
                return isset($data['phone']) && $data['phone'] === '+6281234567890' &&
                       isset($data['email']) && $data['email'] === 'test@example.com' &&
                       isset($data['password']) && is_string($data['password']) &&
                       isset($data['status']) && $data['status'] instanceof UserStatus;
            }))
            ->andReturn($expectedUser);

        // Mock verification service call
        $this->verificationServiceMock
            ->shouldReceive('sendOtp')
            ->with('uuid-123', \Modules\Authentication\Enums\IdentifierType::PHONE)
            ->andReturn('otp-sent-message');

        $result = $this->userService->register(
            '+6281234567890',
            'test@example.com',
            'Password123!'
        );

        $this->assertEquals($expectedUser, $result);
    }

    /**
     * Test registration with existing phone
     *
     * @return void
     */
    public function test_register_with_existing_phone()
    {
        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with('+6281234567890')
            ->andReturn(true);

        $this->expectException(\Modules\Authentication\Exceptions\UserAlreadyExistsException::class);

        $this->userService->register(
            '+6281234567890',
            'test@example.com',
            'Password123!'
        );
    }

    /**
     * Test registration with existing email
     *
     * @return void
     */
    public function test_register_with_existing_email()
    {
        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with('+6281234567890')
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with('test@example.com')
            ->andReturn(true);

        $this->expectException(\Modules\Authentication\Exceptions\UserAlreadyExistsException::class);

        $this->userService->register(
            '+6281234567890',
            'test@example.com',
            'Password123!'
        );
    }

    /**
     * Test successful login
     *
     * @return void
     */
    public function test_login_success()
    {
        $user = new User([
            'id' => 'uuid-123',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        $expectedTokens = [
            'access_token' => 'access-token-123',
            'refresh_token' => 'refresh-token-456',
        ];

        // Mock repository call
        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with('test@example.com')
            ->andReturn($user);

        // Mock JWT service calls
        $this->jwtServiceMock
            ->shouldReceive('generateTokens')
            ->with($user)
            ->andReturn($expectedTokens);

        $result = $this->userService->login('test@example.com', 'Password123!');

        $this->assertEquals($user, $result['user']);
        $this->assertEquals('access-token-123', $result['access_token']);
        $this->assertEquals('refresh-token-456', $result['refresh_token']);
    }

    /**
     * Test login with non-existent user
     *
     * @return void
     */
    public function test_login_user_not_found()
    {
        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with('nonexistent@example.com')
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\UserNotFoundException::class);

        $this->userService->login('nonexistent@example.com', 'Password123!');
    }

    /**
     * Test login with invalid password
     *
     * @return void
     */
    public function test_login_invalid_password()
    {
        $user = new User([
            'id' => 'uuid-123',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with('test@example.com')
            ->andReturn($user);

        $this->expectException(\Modules\Authentication\Exceptions\InvalidCredentialsException::class);

        $this->userService->login('test@example.com', 'WrongPassword!');
    }

    /**
     * Test successful token refresh
     *
     * @return void
     */
    public function test_refresh_token_success()
    {
        $newTokens = [
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
        ];

        $this->jwtServiceMock
            ->shouldReceive('refreshAccessToken')
            ->with('valid-refresh-token')
            ->andReturn($newTokens);

        $result = $this->userService->refreshToken('valid-refresh-token');

        $this->assertEquals($newTokens, $result);
    }

    /**
     * Test refresh token with invalid token
     *
     * @return void
     */
    public function test_refresh_token_invalid()
    {
        $this->jwtServiceMock
            ->shouldReceive('refreshAccessToken')
            ->with('invalid-token')
            ->andReturn(null);

        $result = $this->userService->refreshToken('invalid-token');

        $this->assertNull($result);
    }

    /**
     * Test successful logout
     *
     * @return void
     */
    public function test_logout_success()
    {
        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with('valid-refresh-token')
            ->andReturn(true);

        $result = $this->userService->logout('valid-refresh-token');

        $this->assertTrue($result);
    }

    /**
     * Test logout with invalid token
     *
     * @return void
     */
    public function test_logout_invalid_token()
    {
        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with('invalid-token')
            ->andReturn(false);

        $result = $this->userService->logout('invalid-token');

        $this->assertFalse($result);
    }

    /**
     * Test get user by ID
     *
     * @return void
     */
    public function test_get_user_by_id()
    {
        $user = new User([
            'id' => 'uuid-123',
            'email' => 'test@example.com',
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with('uuid-123')
            ->andReturn($user);

        $result = $this->userService->getUserById('uuid-123');

        $this->assertEquals($user, $result);
    }

    /**
     * Test get user by ID throws exception when not found
     *
     * @return void
     */
    public function test_get_user_by_id_not_found()
    {
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with('nonexistent-id')
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\UserNotFoundException::class);

        $this->userService->getUserById('nonexistent-id');
    }
}