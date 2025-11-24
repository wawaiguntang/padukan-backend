<?php

namespace Modules\Authentication\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Models\PasswordResetToken;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\PasswordReset\IPasswordResetRepository;
use Modules\Authentication\Repositories\User\IUserRepository;
use Tests\TestCase;

/**
 * Password Reset Service Test
 *
 * Tests all PasswordResetService functionality including forgot password and reset operations
 */
class PasswordResetServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The PasswordResetService instance
     *
     * @var \Modules\Authentication\Services\PasswordReset\PasswordResetService
     */
    protected $passwordResetService;

    /**
     * Mocked repositories
     */
    protected $passwordResetRepositoryMock;
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
        $this->passwordResetRepositoryMock = \Mockery::mock(IPasswordResetRepository::class);
        $this->userRepositoryMock = \Mockery::mock(IUserRepository::class);

        // Create service instance with mocks
        $this->passwordResetService = new \Modules\Authentication\Services\PasswordReset\PasswordResetService(
            $this->passwordResetRepositoryMock,
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
     * Test successful forgot password request
     *
     * @return void
     */
    public function test_forgot_password_success()
    {
        $identifier = 'test@example.com';

        $user = new User([
            'id' => 'uuid-123',
            'email' => $identifier,
        ]);

        $resetToken = new PasswordResetToken([
            'id' => 'reset-uuid',
            'user_id' => $user->id,
            'token' => 'reset-token-123',
            'is_used' => false,
        ]);

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with($identifier)
            ->andReturn($user);

        $this->passwordResetRepositoryMock
            ->shouldReceive('createResetToken')
            ->with($user->id, \Mockery::type('string'), 60)
            ->andReturn($resetToken);

        $result = $this->passwordResetService->forgotPassword($identifier);

        $this->assertEquals('auth.password_reset.sent', $result);
    }

    /**
     * Test forgot password with non-existent user
     *
     * @return void
     */
    public function test_forgot_password_user_not_found()
    {
        $identifier = 'nonexistent@example.com';

        $this->userRepositoryMock
            ->shouldReceive('findByIdentifier')
            ->with($identifier)
            ->andReturn(null);

        $result = $this->passwordResetService->forgotPassword($identifier);

        // Should still return success for security (don't reveal if user exists)
        $this->assertEquals('auth.password_reset.sent', $result);
    }

    /**
     * Test successful password reset
     *
     * @return void
     */
    public function test_reset_password_success()
    {
        $userId = 'uuid-123';
        $token = 'reset-token-123';
        $newPassword = 'NewSecurePass123!';

        $user = new User([
            'id' => $userId,
            'email' => 'test@example.com',
            'password' => bcrypt('OldPassword123!'),
        ]);

        $resetToken = new PasswordResetToken([
            'id' => 'reset-uuid',
            'user_id' => $userId,
            'token' => $token,
            'is_used' => false,
        ]);

        // Mock repository calls
        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->passwordResetRepositoryMock
            ->shouldReceive('findValidResetToken')
            ->with($userId, $token)
            ->andReturn($resetToken);

        $this->passwordResetRepositoryMock
            ->shouldReceive('markResetTokenUsed')
            ->with('reset-uuid')
            ->andReturn(true);

        $result = $this->passwordResetService->resetPassword($userId, $token, $newPassword);

        $this->assertEquals('auth.password_reset.success', $result);

        // Verify password was updated (would need to check the actual user update in real implementation)
    }

    /**
     * Test password reset with non-existent user
     *
     * @return void
     */
    public function test_reset_password_user_not_found()
    {
        $userId = 'nonexistent-id';
        $token = 'reset-token-123';
        $newPassword = 'NewSecurePass123!';

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\UserNotFoundException::class);

        $this->passwordResetService->resetPassword($userId, $token, $newPassword);
    }

    /**
     * Test password reset with invalid token
     *
     * @return void
     */
    public function test_reset_password_invalid_token()
    {
        $userId = 'uuid-123';
        $token = 'invalid-token';
        $newPassword = 'NewSecurePass123!';

        $user = new User([
            'id' => $userId,
            'email' => 'test@example.com',
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->passwordResetRepositoryMock
            ->shouldReceive('findValidResetToken')
            ->with($userId, $token)
            ->andReturn(null);

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->passwordResetService->resetPassword($userId, $token, $newPassword);
    }

    /**
     * Test password reset with expired token
     *
     * @return void
     */
    public function test_reset_password_expired_token()
    {
        $userId = 'uuid-123';
        $token = 'expired-token';
        $newPassword = 'NewSecurePass123!';

        $user = new User([
            'id' => $userId,
            'email' => 'test@example.com',
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->passwordResetRepositoryMock
            ->shouldReceive('findValidResetToken')
            ->with($userId, $token)
            ->andReturn(null); // Repository returns null for expired tokens

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->passwordResetService->resetPassword($userId, $token, $newPassword);
    }

    /**
     * Test password reset with already used token
     *
     * @return void
     */
    public function test_reset_password_used_token()
    {
        $userId = 'uuid-123';
        $token = 'used-token';
        $newPassword = 'NewSecurePass123!';

        $user = new User([
            'id' => $userId,
            'email' => 'test@example.com',
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findById')
            ->with($userId)
            ->andReturn($user);

        $this->passwordResetRepositoryMock
            ->shouldReceive('findValidResetToken')
            ->with($userId, $token)
            ->andReturn(null); // Repository returns null for used tokens

        $this->expectException(\Modules\Authentication\Exceptions\InvalidTokenException::class);

        $this->passwordResetService->resetPassword($userId, $token, $newPassword);
    }

    /**
     * Test generate reset token
     *
     * @return void
     */
    public function test_generate_reset_token()
    {
        $token = $this->passwordResetService->generateResetToken();

        // Verify token is a string
        $this->assertIsString($token);

        // Verify token is not empty
        $this->assertNotEmpty($token);

        // Verify token length (should be reasonable for a secure token)
        $this->assertGreaterThan(10, strlen($token));
    }

    /**
     * Test generate unique reset tokens
     *
     * @return void
     */
    public function test_generate_unique_reset_tokens()
    {
        $tokens = [];

        // Generate multiple tokens
        for ($i = 0; $i < 100; $i++) {
            $token = $this->passwordResetService->generateResetToken();
            $this->assertNotContains($token, $tokens, "Token is not unique");
            $tokens[] = $token;
        }

        // Verify we got 100 unique tokens
        $this->assertCount(100, array_unique($tokens));
    }
}
