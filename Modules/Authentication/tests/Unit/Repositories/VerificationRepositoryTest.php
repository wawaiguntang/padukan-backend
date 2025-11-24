<?php

namespace Modules\Authentication\Tests\Unit\Repositories;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Modules\Authentication\Database\Factories\UserFactory;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Models\User;
use Modules\Authentication\Models\VerificationToken;
use Modules\Authentication\Repositories\Verification\VerificationRepository;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * Verification Repository Test
 *
 * This test class covers all VerificationRepository functionality
 * including OTP creation, validation, and rate limiting features.
 */
class VerificationRepositoryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * The VerificationRepository instance
     *
     * @var VerificationRepository
     */
    protected VerificationRepository $repository;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new VerificationRepository(new VerificationToken(), Cache::store());
    }

    /**
     * Generate unique phone number for testing
     *
     * @return string
     */
    private function generateUniquePhone(): string
    {
        return $this->faker->numerify('+628##########');
    }

    /**
     * Generate unique email for testing
     *
     * @return string
     */
    private function generateUniqueEmail(): string
    {
        return $this->faker->unique()->email();
    }

    /**
     * Test creating OTP token
     *
     * @return void
     */
    public function test_create_otp()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = $this->faker->numerify('######');
        $expiresInMinutes = 10;

        // Act
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $token, $expiresInMinutes);

        // Assert
        $this->assertInstanceOf(VerificationToken::class, $otp);
        $this->assertEquals($user->id, $otp->user_id);
        $this->assertEquals(IdentifierType::PHONE, $otp->type);
        $this->assertEquals($token, $otp->token);
        $this->assertFalse($otp->is_used);
        $this->assertGreaterThanOrEqual($expiresInMinutes - 1, Carbon::now()->diffInMinutes($otp->expires_at, false));
        $this->assertLessThanOrEqual($expiresInMinutes + 1, Carbon::now()->diffInMinutes($otp->expires_at, false));
    }

    /**
     * Test finding valid OTP token
     *
     * @return void
     */
    public function test_find_valid_otp()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = $this->faker->numerify('######');
        $this->repository->createOtp($user->id, IdentifierType::PHONE, $token, 10);

        // Act
        $foundOtp = $this->repository->findValidOtp($user->id, IdentifierType::PHONE, $token);

        // Assert
        $this->assertNotNull($foundOtp);
        $this->assertEquals($user->id, $foundOtp->user_id);
        $this->assertEquals(IdentifierType::PHONE, $foundOtp->type);
        $this->assertEquals($token, $foundOtp->token);
        $this->assertFalse($foundOtp->is_used);
    }

    /**
     * Test finding valid OTP returns null when token not found
     *
     * @return void
     */
    public function test_find_valid_otp_returns_null_when_not_found()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Act
        $foundOtp = $this->repository->findValidOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'));

        // Assert
        $this->assertNull($foundOtp);
    }

    /**
     * Test finding valid OTP returns null when token is used
     *
     * @return void
     */
    public function test_find_valid_otp_returns_null_when_used()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = $this->faker->numerify('######');
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $token, 10);
        $otp->update(['is_used' => true]);

        // Act
        $foundOtp = $this->repository->findValidOtp($user->id, IdentifierType::PHONE, $token);

        // Assert
        $this->assertNull($foundOtp);
    }

    /**
     * Test finding valid OTP returns null when token is expired
     *
     * @return void
     */
    public function test_find_valid_otp_returns_null_when_expired()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = $this->faker->numerify('######');
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $token, -1); // Already expired

        // Act
        $foundOtp = $this->repository->findValidOtp($user->id, IdentifierType::PHONE, $token);

        // Assert
        $this->assertNull($foundOtp);
    }

    /**
     * Test marking OTP as used
     *
     * @return void
     */
    public function test_mark_otp_used()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $token = $this->faker->numerify('######');
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $token, 10);

        // Act
        $result = $this->repository->markOtpUsed($otp->id);

        // Assert
        $this->assertTrue($result);
        $otp->refresh();
        $this->assertTrue($otp->is_used);
    }

    /**
     * Test marking non-existent OTP returns false
     *
     * @return void
     */
    public function test_mark_otp_used_non_existent_returns_false()
    {
        // Act
        $result = $this->repository->markOtpUsed(Uuid::uuid4()->toString());

        // Assert
        $this->assertFalse($result);
    }

    /**
     * Test deleting expired OTPs
     *
     * @return void
     */
    public function test_delete_expired_otps()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Create expired OTP
        $expiredOtp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), -1);

        // Create valid OTP
        $validOtp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), 10);

        // Act
        $deletedCount = $this->repository->deleteExpiredOtps();

        // Assert
        $this->assertGreaterThanOrEqual(1, $deletedCount); // At least the expired token should be deleted
        $this->assertNull($this->repository->findById($expiredOtp->id));
        $this->assertNotNull($this->repository->findById($validOtp->id));
    }

    /**
     * Test checking if OTP can be sent (rate limiting)
     *
     * @return void
     */
    public function test_can_send_otp()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // No previous OTP sent
        $this->assertTrue($this->repository->canSendOtp($user->id, IdentifierType::PHONE));

        // Create recent OTP
        $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), 10);

        // Should not be able to send within 1 minute
        $this->assertFalse($this->repository->canSendOtp($user->id, IdentifierType::PHONE));
    }

    /**
     * Test getting last OTP sent timestamp
     *
     * @return void
     */
    public function test_get_last_otp_sent_at()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // No OTP sent yet
        $this->assertNull($this->repository->getLastOtpSentAt($user->id, IdentifierType::PHONE));

        // Create OTP
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), 10);

        // Act
        $lastSentAt = $this->repository->getLastOtpSentAt($user->id, IdentifierType::PHONE);

        // Assert
        $this->assertNotNull($lastSentAt);
        $this->assertEquals($otp->created_at->toDateTimeString(), $lastSentAt->toDateTimeString());
    }

    /**
     * Test finding OTP by ID
     *
     * @return void
     */
    public function test_find_by_id()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), 10);

        // Act
        $foundOtp = $this->repository->findById($otp->id);

        // Assert
        $this->assertNotNull($foundOtp);
        $this->assertEquals($otp->id, $foundOtp->id);
    }

    /**
     * Test finding non-existent OTP by ID returns null
     *
     * @return void
     */
    public function test_find_by_id_returns_null_when_not_found()
    {
        // Act
        $foundOtp = $this->repository->findById(Uuid::uuid4()->toString());

        // Assert
        $this->assertNull($foundOtp);
    }

    /**
     * Test deleting OTP
     *
     * @return void
     */
    public function test_delete_otp()
    {
        // Arrange
        $user = UserFactory::new()->create();
        $otp = $this->repository->createOtp($user->id, IdentifierType::PHONE, $this->faker->numerify('######'), 10);

        // Act
        $result = $this->repository->delete($otp->id);

        // Assert
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById($otp->id));
    }

    /**
     * Test deleting non-existent OTP returns false
     *
     * @return void
     */
    public function test_delete_non_existent_otp_returns_false()
    {
        // Act
        $result = $this->repository->delete(Uuid::uuid4()->toString());

        // Assert
        $this->assertFalse($result);
    }
}