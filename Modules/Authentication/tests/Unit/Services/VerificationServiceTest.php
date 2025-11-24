<?php

namespace Modules\Authentication\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Models\User;
use Modules\Authentication\Models\VerificationToken;
use Modules\Authentication\Repositories\Verification\IVerificationRepository;
use Modules\Authentication\Services\Verification\VerificationService;
use Tests\TestCase;

/**
 * Verification Service Test
 *
 * Tests all VerificationService functionality including OTP operations
 */
class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The VerificationService instance
     *
     * @var VerificationService
     */
    protected VerificationService $verificationService;

    /**
     * Mocked repositories
     */
    protected $verificationRepositoryMock;
    protected $userRepositoryMock;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks using Mockery
        $this->verificationRepositoryMock = \Mockery::mock(IVerificationRepository::class);
        $this->userRepositoryMock = \Mockery::mock(\Modules\Authentication\Repositories\User\IUserRepository::class);

        // Create service instance with mocks
        $this->verificationService = new \Modules\Authentication\Services\Verification\VerificationService(
            $this->verificationRepositoryMock,
            $this->userRepositoryMock
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
     * Test successful OTP send
     *
     * @return void
     */
    public function test_send_otp_success()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;

        $user = new \Modules\Authentication\Models\User([
            'id' => $userId,
            'phone' => '+6281234567890',
        ]);

        $otpToken = new VerificationToken([
            'id' => 'otp-uuid',
            'user_id' => $userId,
            'type' => $type,
            'token' => '123456',
            'is_used' => false,
        ]);

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->verificationRepositoryMock
            ->shouldReceive('canSendOtp')
            ->with($userId, $type)
            ->andReturn(true);

        $this->verificationRepositoryMock
            ->shouldReceive('createOtp')
            ->with($userId, $type, \Mockery::type('string'), 5)
            ->andReturn($otpToken);

        $result = $this->verificationService->sendOtp($userId, $type);

        $this->assertEquals('auth.otp.sent', $result);
    }

    /**
     * Test OTP send when rate limited
     *
     * @return void
     */
    public function test_send_otp_rate_limited()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;

        $this->verificationRepositoryMock
            ->shouldReceive('canSendOtp')
            ->with($userId, $type)
            ->andReturn(false);

        $this->expectException(\Modules\Authentication\Exceptions\RateLimitExceededException::class);

        $this->verificationService->sendOtp($userId, $type);
    }

    /**
     * Test successful OTP resend
     *
     * @return void
     */
    public function test_resend_otp_success()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;

        $user = new \Modules\Authentication\Models\User([
            'id' => $userId,
            'phone' => '+6281234567890',
        ]);

        $otpToken = new VerificationToken([
            'id' => 'otp-uuid',
            'user_id' => $userId,
            'type' => $type,
            'token' => '123456',
            'is_used' => false,
        ]);

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->verificationRepositoryMock
            ->shouldReceive('canSendOtp')
            ->with($userId, $type)
            ->andReturn(true);

        $this->verificationRepositoryMock
            ->shouldReceive('createOtp')
            ->with($userId, $type, \Mockery::type('string'), 5)
            ->andReturn($otpToken);

        $result = $this->verificationService->resendOtp($userId, $type);

        $this->assertEquals('auth.otp.resent', $result);
    }

    /**
     * Test successful OTP validation
     *
     * @return void
     */
    public function test_validate_otp_success()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;
        $token = '123456';

        $otpToken = new VerificationToken([
            'id' => 'otp-uuid',
            'user_id' => $userId,
            'type' => $type,
            'token' => $token,
            'is_used' => false,
        ]);

        // Mock repository calls
        $this->verificationRepositoryMock
            ->shouldReceive('findValidOtp')
            ->with($userId, $type, $token)
            ->andReturn($otpToken);

        $this->verificationRepositoryMock
            ->shouldReceive('markOtpUsed')
            ->with('otp-uuid')
            ->andReturn(true);

        $result = $this->verificationService->validateOtp($userId, $type, $token);

        $this->assertEquals('auth.otp.validated', $result);
    }

    /**
     * Test OTP validation with invalid token
     *
     * @return void
     */
    public function test_validate_otp_invalid_token()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;
        $token = '999999';

        $this->verificationRepositoryMock
            ->shouldReceive('findValidOtp')
            ->with($userId, $type, $token)
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->verificationService->validateOtp($userId, $type, $token);
    }

    /**
     * Test OTP validation with expired token
     *
     * @return void
     */
    public function test_validate_otp_expired_token()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;
        $token = '123456';

        // Create expired token
        $expiredToken = new VerificationToken([
            'id' => 'otp-uuid',
            'user_id' => $userId,
            'type' => $type,
            'token' => $token,
            'is_used' => false,
            'expires_at' => now()->subMinutes(1),
        ]);

        $this->verificationRepositoryMock
            ->shouldReceive('findValidOtp')
            ->with($userId, $type, $token)
            ->andReturn(null); // Repository returns null for expired tokens

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->verificationService->validateOtp($userId, $type, $token);
    }

    /**
     * Test OTP validation with already used token
     *
     * @return void
     */
    public function test_validate_otp_already_used_token()
    {
        $userId = 'uuid-123';
        $type = IdentifierType::PHONE;
        $token = '123456';

        $this->verificationRepositoryMock
            ->shouldReceive('findValidOtp')
            ->with($userId, $type, $token)
            ->andReturn(null); // Repository returns null for used tokens

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->verificationService->validateOtp($userId, $type, $token);
    }

}