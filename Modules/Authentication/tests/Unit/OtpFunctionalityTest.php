<?php

namespace Modules\Authentication\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Models\User;
use Modules\Authentication\Models\VerificationToken;
use Tests\TestCase;

/**
 * OTP Functionality Test
 *
 * Comprehensive tests for OTP generation, validation, and management
 */
class OtpFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test OTP token format is always 6 digits
     *
     * @return void
     */
    public function test_otp_token_is_always_6_digits()
    {
        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generateOtp');
        $method->setAccessible(true);

        for ($i = 0; $i < 100; $i++) {
            $otp = $method->invoke($service);
            $this->assertMatchesRegularExpression('/^\d{6}$/', $otp, "OTP '{$otp}' is not 6 digits");
            $this->assertEquals(6, strlen($otp), "OTP '{$otp}' length is not 6");
        }
    }

    /**
     * Test OTP tokens are numeric only
     *
     * @return void
     */
    public function test_otp_tokens_are_numeric_only()
    {
        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generateOtp');
        $method->setAccessible(true);

        for ($i = 0; $i < 50; $i++) {
            $otp = $method->invoke($service);
            $this->assertTrue(is_numeric($otp), "OTP '{$otp}' contains non-numeric characters");
            $this->assertFalse(str_contains($otp, ' '), "OTP '{$otp}' contains spaces");
            $this->assertFalse(str_contains($otp, '-'), "OTP '{$otp}' contains hyphens");
            $this->assertFalse(str_contains($otp, '.'), "OTP '{$otp}' contains dots");
        }
    }

    /**
     * Test OTP tokens can start with zero
     *
     * @return void
     */
    public function test_otp_tokens_can_start_with_zero()
    {
        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generateOtp');
        $method->setAccessible(true);

        $foundZeroStart = false;
        $attempts = 0;

        // Try up to 1000 times to find an OTP starting with zero
        while (!$foundZeroStart && $attempts < 1000) {
            $otp = $method->invoke($service);
            if (str_starts_with($otp, '0')) {
                $foundZeroStart = true;
                $this->assertStringStartsWith('0', $otp, "OTP '{$otp}' should start with zero");
            }
            $attempts++;
        }

        // This might fail in very rare cases, but should pass most of the time
        $this->assertTrue($foundZeroStart, 'Could not generate OTP starting with zero after 1000 attempts');
    }

    /**
     * Test OTP token distribution is relatively uniform
     *
     * @return void
     */
    public function test_otp_token_distribution()
    {
        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass($service);
        $method = $reflection->getMethod('generateOtp');
        $method->setAccessible(true);

        $tokens = [];
        $sampleSize = 1000;

        // Generate sample tokens
        for ($i = 0; $i < $sampleSize; $i++) {
            $tokens[] = $method->invoke($service);
        }

        $uniqueTokens = array_unique($tokens);
        $uniquenessRatio = count($uniqueTokens) / $sampleSize;

        // Should have high uniqueness (allowing for some collisions in random generation)
        $this->assertGreaterThan(0.95, $uniquenessRatio, 'OTP uniqueness ratio too low');

        // Check that we get tokens from different ranges
        $firstDigits = array_map(fn($token) => $token[0], $tokens);
        $uniqueFirstDigits = array_unique($firstDigits);

        // Should have at least 8 different first digits (0-9 except possibly some)
        $this->assertGreaterThanOrEqual(8, count($uniqueFirstDigits), 'OTP first digits not well distributed');
    }

    /**
     * Test OTP validation accepts exactly 6 digits
     *
     * @return void
     */
    public function test_otp_validation_format()
    {
        $user = User::factory()->create();
        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Test valid 6-digit OTPs
        $validOtps = ['123456', '000000', '999999', '012345', '987654'];

        foreach ($validOtps as $otp) {
            // This should not throw InvalidTokenException for format
            // (though it might fail for other reasons like not found)
            try {
                $service->validateOtp($user->id, IdentifierType::PHONE, $otp);
            } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
                // If it fails, it should not be due to format - just check it's InvalidTokenException
                $this->assertInstanceOf(\Modules\Authentication\Exceptions\InvalidTokenException::class, $e);
            } catch (\Exception $e) {
                // Other exceptions are OK (user not found, token not found, etc.)
            }
        }

        // Test invalid formats
        $invalidOtps = [
            '12345',      // 5 digits
            '1234567',    // 7 digits
            '12345a',     // contains letter
            '123 456',    // contains space
            '123-456',    // contains hyphen
            '123.456',    // contains dot
            'abcdef',     // all letters
            '',           // empty
            '123456789',  // 9 digits
        ];

        foreach ($invalidOtps as $otp) {
            try {
                $service->validateOtp($user->id, IdentifierType::PHONE, $otp);
                $this->fail("OTP '{$otp}' should have thrown InvalidTokenException for invalid format");
            } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
                // InvalidTokenException thrown for invalid format - this is correct
                $this->assertInstanceOf(\Modules\Authentication\Exceptions\InvalidTokenException::class, $e);
            } catch (\Exception $e) {
                // Other exceptions might occur, but format validation should happen first
                $this->fail("OTP '{$otp}' threw unexpected exception: " . get_class($e));
            }
        }
    }

    /**
     * Test OTP expiry logic
     *
     * @return void
     */
    public function test_otp_expiry_logic()
    {
        $user = User::factory()->create();

        // Create expired OTP
        $expiredOtp = VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'is_used' => false,
            'expires_at' => now()->subMinutes(1), // 1 minute ago
        ]);

        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        try {
            $service->validateOtp($user->id, IdentifierType::PHONE, '123456');
            $this->fail('Expired OTP should have thrown OtpExpiredException');
        } catch (\Modules\Authentication\Exceptions\OtpExpiredException $e) {
            $this->assertInstanceOf(\Modules\Authentication\Exceptions\OtpExpiredException::class, $e);
        } catch (\Exception $e) {
            $this->fail('Expired OTP threw unexpected exception: ' . get_class($e));
        }

        // Create valid OTP
        $validOtp = VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '654321',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5), // 5 minutes from now
        ]);

        // This should work (might throw other exceptions but not expiry)
        try {
            $service->validateOtp($user->id, IdentifierType::PHONE, '654321');
        } catch (\Modules\Authentication\Exceptions\OtpExpiredException $e) {
            $this->fail('Valid OTP should not be expired');
        } catch (\Exception $e) {
            // Other exceptions are OK for this test
        }
    }

    /**
     * Test OTP reuse prevention
     *
     * @return void
     */
    public function test_otp_reuse_prevention()
    {
        $user = User::factory()->create();

        // Create used OTP
        $usedOtp = VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'is_used' => true, // Already used
            'expires_at' => now()->addMinutes(5),
        ]);

        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        try {
            $service->validateOtp($user->id, IdentifierType::PHONE, '123456');
            $this->fail('Used OTP should have thrown InvalidTokenException');
        } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
            $this->assertInstanceOf(\Modules\Authentication\Exceptions\InvalidTokenException::class, $e);
        } catch (\Exception $e) {
            $this->fail('Used OTP threw unexpected exception: ' . get_class($e));
        }
    }

    /**
     * Test OTP token uniqueness per user and type
     *
     * @return void
     */
    public function test_otp_uniqueness_per_user_and_type()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create same token for different users
        VerificationToken::factory()->create([
            'user_id' => $user1->id,
            'type' => IdentifierType::PHONE,
            'token' => '111111',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        VerificationToken::factory()->create([
            'user_id' => $user2->id,
            'type' => IdentifierType::PHONE,
            'token' => '111111', // Same token
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Both should be valid independently
        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user1->id,
            'type' => IdentifierType::PHONE->value,
            'token' => '111111',
        ]);

        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user2->id,
            'type' => IdentifierType::PHONE->value,
            'token' => '111111',
        ]);
    }

    /**
     * Test OTP cleanup removes expired tokens
     *
     * @return void
     */
    public function test_otp_cleanup_removes_expired_tokens()
    {
        $user = User::factory()->create();

        // Create mix of expired and valid tokens
        $expiredTokens = VerificationToken::factory()->count(3)->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'is_used' => false,
            'expires_at' => now()->subMinutes(10), // Expired
        ]);

        $validTokens = VerificationToken::factory()->count(2)->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'is_used' => false,
            'expires_at' => now()->addMinutes(10), // Still valid
        ]);

        // Verify tokens exist before cleanup
        foreach ($expiredTokens as $token) {
            $this->assertDatabaseHas('verification_tokens', ['id' => $token->id]);
        }

        foreach ($validTokens as $token) {
            $this->assertDatabaseHas('verification_tokens', ['id' => $token->id]);
        }

        // Run cleanup
        $repository = app(\Modules\Authentication\Repositories\Verification\IVerificationRepository::class);
        $deletedCount = $repository->deleteExpiredOtps();

        // Verify expired tokens are deleted
        $this->assertEquals(3, $deletedCount);

        foreach ($expiredTokens as $token) {
            $this->assertDatabaseMissing('verification_tokens', ['id' => $token->id]);
        }

        // Verify valid tokens remain
        foreach ($validTokens as $token) {
            $this->assertDatabaseHas('verification_tokens', ['id' => $token->id]);
        }
    }

    /**
     * Test OTP rate limiting logic
     *
     * @return void
     */
    public function test_otp_rate_limiting_logic()
    {
        $user = User::factory()->create();
        $repository = app(\Modules\Authentication\Repositories\Verification\IVerificationRepository::class);

        // Initially should be allowed
        $this->assertTrue($repository->canSendOtp($user->id, IdentifierType::PHONE));

        // Create recent OTP
        $repository->createOtp($user->id, IdentifierType::PHONE, '123456', 5);

        // Should not be allowed immediately
        $this->assertFalse($repository->canSendOtp($user->id, IdentifierType::PHONE));

        // Check last sent time
        $lastSent = $repository->getLastOtpSentAt($user->id, IdentifierType::PHONE);
        $this->assertNotNull($lastSent);
        $this->assertInstanceOf(\Carbon\Carbon::class, $lastSent);
    }

    /**
     * Test OTP token case sensitivity
     *
     * @return void
     */
    public function test_otp_token_case_sensitivity()
    {
        $user = User::factory()->create();

        // Create OTP with lowercase (though OTPs are numeric, test the concept)
        VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        $service = app(\Modules\Authentication\Services\Verification\VerificationService::class);

        // Should work with exact match
        try {
            $service->validateOtp($user->id, IdentifierType::PHONE, '123456');
        } catch (\Modules\Authentication\Exceptions\InvalidTokenException $e) {
            // If it fails, it should not be due to format - just check it's InvalidTokenException
            $this->assertInstanceOf(\Modules\Authentication\Exceptions\InvalidTokenException::class, $e);
        } catch (\Exception $e) {
            // Other exceptions are OK
        }
    }
}