<?php

namespace Modules\Authentication\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    use RefreshDatabase, WithFaker;

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
        $phone = $this->faker->numerify('+628##########');
        $email = $this->faker->email();
        $password = $this->faker->password();
        $userId = $this->faker->uuid();

        $userData = [
            'phone' => $phone,
            'email' => $email,
            'password' => $password,
        ];

        $expectedUser = new User([
            'id' => $userId,
            'phone' => $phone,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatus::PENDING,
        ]);
        $expectedUser->id = $userId; // Ensure ID is set

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with($phone)
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with($email)
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($data) use ($phone, $email) {
                return isset($data['phone']) && $data['phone'] === $phone &&
                       isset($data['email']) && $data['email'] === $email &&
                       isset($data['password']) && is_string($data['password']) &&
                       isset($data['status']) && $data['status'] instanceof UserStatus;
            }))
            ->andReturn($expectedUser);

        // Mock verification service call
        $this->verificationServiceMock
            ->shouldReceive('sendOtp')
            ->with($userId, \Modules\Authentication\Enums\IdentifierType::PHONE)
            ->andReturn('otp-sent-message');

        $result = $this->userService->register(
            $phone,
            $email,
            $password
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
        $phone = $this->faker->numerify('+628##########');
        $email = $this->faker->email();
        $password = $this->faker->password();

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with($phone)
            ->andReturn(true);

        $this->expectException(\Modules\Authentication\Exceptions\UserAlreadyExistsException::class);

        $this->userService->register(
            $phone,
            $email,
            $password
        );
    }

    /**
     * Test registration with existing email
     *
     * @return void
     */
    public function test_register_with_existing_email()
    {
        $phone = $this->faker->numerify('+628##########');
        $email = $this->faker->email();
        $password = $this->faker->password();

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with($phone)
            ->andReturn(false);

        $this->userRepositoryMock
            ->shouldReceive('existsByIdentifier')
            ->with($email)
            ->andReturn(true);

        $this->expectException(\Modules\Authentication\Exceptions\UserAlreadyExistsException::class);

        $this->userService->register(
            $phone,
            $email,
            $password
        );
    }

    /**
     * Test successful login
     *
     * @return void
     */
    public function test_login_success()
    {
        $userId = $this->faker->uuid();
        $email = $this->faker->email();
        $password = $this->faker->password();
        $accessToken = $this->faker->sha256();
        $refreshToken = $this->faker->sha256();

        $user = new User([
            'id' => $userId,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatus::ACTIVE,
        ]);

        $expectedTokens = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];

        // Mock repository call
        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with($email)
            ->andReturn($user);

        // Mock JWT service calls
        $this->jwtServiceMock
            ->shouldReceive('generateTokens')
            ->with($user)
            ->andReturn($expectedTokens);

        $result = $this->userService->login($email, $password);

        $this->assertEquals($user, $result['user']);
        $this->assertEquals($accessToken, $result['access_token']);
        $this->assertEquals($refreshToken, $result['refresh_token']);
    }

    /**
     * Test login with non-existent user
     *
     * @return void
     */
    public function test_login_user_not_found()
    {
        $email = $this->faker->email();
        $password = $this->faker->password();

        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with($email)
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\UserNotFoundException::class);

        $this->userService->login($email, $password);
    }

    /**
     * Test login with invalid password
     *
     * @return void
     */
    public function test_login_invalid_password()
    {
        $userId = $this->faker->uuid();
        $email = $this->faker->email();
        $password = $this->faker->password();
        $wrongPassword = $this->faker->password();

        $user = new User([
            'id' => $userId,
            'email' => $email,
            'password' => Hash::make($password),
            'status' => UserStatus::ACTIVE,
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with($email)
            ->andReturn($user);

        $this->expectException(\Modules\Authentication\Exceptions\InvalidCredentialsException::class);

        $this->userService->login($email, $wrongPassword);
    }

    /**
     * Test successful token refresh
     *
     * @return void
     */
    public function test_refresh_token_success()
    {
        $validRefreshToken = $this->faker->sha256();
        $newAccessToken = $this->faker->sha256();
        $newRefreshToken = $this->faker->sha256();

        $newTokens = [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ];

        $this->jwtServiceMock
            ->shouldReceive('refreshAccessToken')
            ->with($validRefreshToken)
            ->andReturn($newTokens);

        $result = $this->userService->refreshToken($validRefreshToken);

        $this->assertEquals($newTokens, $result);
    }

    /**
     * Test refresh token with invalid token
     *
     * @return void
     */
    public function test_refresh_token_invalid()
    {
        $invalidToken = $this->faker->sha256();

        $this->jwtServiceMock
            ->shouldReceive('refreshAccessToken')
            ->with($invalidToken)
            ->andReturn(null);

        $result = $this->userService->refreshToken($invalidToken);

        $this->assertNull($result);
    }

    /**
     * Test successful logout
     *
     * @return void
     */
    public function test_logout_success()
    {
        $validRefreshToken = $this->faker->sha256();

        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with($validRefreshToken)
            ->andReturn(true);

        $result = $this->userService->logout($validRefreshToken);

        $this->assertTrue($result);
    }

    /**
     * Test logout with invalid token
     *
     * @return void
     */
    public function test_logout_invalid_token()
    {
        $invalidToken = $this->faker->sha256();

        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with($invalidToken)
            ->andReturn(false);

        $result = $this->userService->logout($invalidToken);

        $this->assertFalse($result);
    }

    /**
     * Test get user by ID
     *
     * @return void
     */
    public function test_get_user_by_id()
    {
        $userId = $this->faker->uuid();
        $email = $this->faker->email();

        $user = new User([
            'id' => $userId,
            'email' => $email,
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $result = $this->userService->getUserById($userId);

        $this->assertEquals($user, $result);
    }

    /**
     * Test get user by ID throws exception when not found
     *
     * @return void
     */
    public function test_get_user_by_id_not_found()
    {
        $nonexistentId = $this->faker->uuid();

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($nonexistentId)
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\UserNotFoundException::class);

        $this->userService->getUserById($nonexistentId);
    }
}