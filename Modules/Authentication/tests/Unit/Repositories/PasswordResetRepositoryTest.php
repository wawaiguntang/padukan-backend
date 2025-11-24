<?php

namespace Modules\Authentication\Tests\Unit\Repositories;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Modules\Authentication\Database\Factories\UserFactory;
use Modules\Authentication\Models\PasswordResetToken;
use Modules\Authentication\Models\User;
use Modules\Authentication\Repositories\PasswordReset\PasswordResetRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * Password Reset Repository Test
 *
 * This test class covers all PasswordResetRepository functionality
 * including password reset token creation, validation, and management.
 */
class PasswordResetRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The PasswordResetRepository instance
     *
     * @var PasswordResetRepository
     */
    protected PasswordResetRepository $repository;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new PasswordResetRepository(new PasswordResetToken(), Cache::store());
    }

    /**
     * Test creating password reset token
     *
     * @return void
     */
    public function test_create_reset_token()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = 'reset-token-123';
        $expiresInMinutes = 30;

        // Act
        $resetToken = $this->repository->createResetToken($user->id, $token, $expiresInMinutes);

        // Assert
        $this->assertInstanceOf(PasswordResetToken::class, $resetToken);
        $this->assertEquals($user->id, $resetToken->user_id);
        $this->assertEquals($token, $resetToken->token);
        $this->assertFalse($resetToken->is_used);
        $this->assertGreaterThanOrEqual($expiresInMinutes - 1, Carbon::now()->diffInMinutes($resetToken->expires_at, false));
        $this->assertLessThanOrEqual($expiresInMinutes + 1, Carbon::now()->diffInMinutes($resetToken->expires_at, false));
    }

    /**
     * Test finding valid password reset token
     *
     * @return void
     */
    public function test_find_valid_reset_token()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = 'reset-token-123';
        $this->repository->createResetToken($user->id, $token, 60);

        // Act
        $foundToken = $this->repository->findValidResetToken($user->id, $token);

        // Assert
        $this->assertNotNull($foundToken);
        $this->assertEquals($user->id, $foundToken->user_id);
        $this->assertEquals($token, $foundToken->token);
        $this->assertFalse($foundToken->is_used);
    }

    /**
     * Test finding valid reset token returns null when token not found
     *
     * @return void
     */
    public function test_find_valid_reset_token_returns_null_when_not_found()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Act
        $foundToken = $this->repository->findValidResetToken($user->id, 'nonexistent-token');

        // Assert
        $this->assertNull($foundToken);
    }

    /**
     * Test finding valid reset token returns null when token is used
     *
     * @return void
     */
    public function test_find_valid_reset_token_returns_null_when_used()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = 'reset-token-123';
        $resetToken = $this->repository->createResetToken($user->id, $token, 60);
        $resetToken->update(['is_used' => true]);

        // Act
        $foundToken = $this->repository->findValidResetToken($user->id, $token);

        // Assert
        $this->assertNull($foundToken);
    }

    /**
     * Test finding valid reset token returns null when token is expired
     *
     * @return void
     */
    public function test_find_valid_reset_token_returns_null_when_expired()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = 'reset-token-123';
        $resetToken = $this->repository->createResetToken($user->id, $token, -1); // Already expired

        // Act
        $foundToken = $this->repository->findValidResetToken($user->id, $token);

        // Assert
        $this->assertNull($foundToken);
    }

    /**
     * Test marking password reset token as used
     *
     * @return void
     */
    public function test_mark_reset_token_used()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = 'reset-token-123';
        $resetToken = $this->repository->createResetToken($user->id, $token, 60);

        // Act
        $result = $this->repository->markResetTokenUsed($resetToken->id);

        // Assert
        $this->assertTrue($result);
        $resetToken->refresh();
        $this->assertTrue($resetToken->is_used);
    }

    /**
     * Test marking non-existent reset token returns false
     *
     * @return void
     */
    public function test_mark_reset_token_used_non_existent_returns_false()
    {
        // Act
        $result = $this->repository->markResetTokenUsed(Uuid::uuid4()->toString());

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test deleting expired password reset tokens
     *
     * @return void
     */
    public function test_delete_expired_reset_tokens()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Create expired token
        $expiredToken = $this->repository->createResetToken($user->id, 'expired-token', -1);

        // Create valid token
        $validToken = $this->repository->createResetToken($user->id, 'valid-token', 60);

        // Act
        $deletedCount = $this->repository->deleteExpiredResetTokens();

        // Assert
        $this->assertGreaterThanOrEqual(1, $deletedCount); // At least the expired token should be deleted
        $this->assertNull($this->repository->findById($expiredToken->id));
        $this->assertNotNull($this->repository->findById($validToken->id));
    }

    /**
     * Test finding password reset token by ID
     *
     * @return void
     */
    public function test_find_by_id()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $resetToken = $this->repository->createResetToken($user->id, 'reset-token-123', 60);

        // Act
        $foundToken = $this->repository->findById($resetToken->id);

        // Assert
        $this->assertNotNull($foundToken);
        $this->assertEquals($resetToken->id, $foundToken->id);
    }

    /**
     * Test finding non-existent password reset token by ID returns null
     *
     * @return void
     */
    public function test_find_by_id_returns_null_when_not_found()
    {
        // Act
        $foundToken = $this->repository->findById(Uuid::uuid4()->toString());

        // Assert
        $this->assertNull($foundToken);
    }

    /**
     * Test deleting password reset token
     *
     * @return void
     */
    public function test_delete_reset_token()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $resetToken = $this->repository->createResetToken($user->id, 'reset-token-123', 60);

        // Act
        $result = $this->repository->delete($resetToken->id);

        // Assert
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById($resetToken->id));
    }

    /**
     * Test deleting non-existent password reset token returns false
     *
     * @return void
     */
    public function test_delete_non_existent_reset_token_returns_false()
    {
        // Act
        $result = $this->repository->delete(Uuid::uuid4()->toString());

        // Assert
        $this->assertFalse($result);
    }
}