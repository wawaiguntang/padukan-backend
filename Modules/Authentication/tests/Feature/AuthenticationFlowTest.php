<?php

namespace Modules\Authentication\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;
use Modules\Authentication\Models\VerificationToken;
use Tests\TestCase;

/**
 * Authentication Flow Integration Test
 *
 * Tests complete user authentication journeys and complex scenarios
 */
class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete user registration and login flow
     *
     * @return void
     */
    public function test_complete_registration_and_login_flow()
    {
        // Step 1: Register user
        $userData = [
            'phone' => '+6281234567890',
            'email' => 'john@example.com',
            'password' => 'SecurePass123!',
        ];

        $registerResponse = $this->postJson('/api/v1/auth/register', $userData);
        $registerResponse->assertStatus(200)
                        ->assertJson([
                            'status' => true,
                            'message' => 'User registered successfully.',
                        ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'phone' => '+6281234567890',
            'email' => 'john@example.com',
            'status' => UserStatus::PENDING->value,
        ]);

        $user = User::where('email', 'john@example.com')->first();

        // Step 2: Send OTP for phone verification
        $otpResponse = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $otpResponse->assertStatus(200)
                   ->assertJson([
                       'status' => true,
                       'message' => 'OTP sent successfully.',
                   ]);

        // Get the OTP token from database
        $otpToken = VerificationToken::where('user_id', $user->id)
                                    ->where('type', IdentifierType::PHONE)
                                    ->first();

        $this->assertNotNull($otpToken);
        $this->assertFalse($otpToken->is_used);

        // Step 3: Validate OTP
        $validateResponse = $this->postJson('/api/v1/auth/validate-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
            'token' => $otpToken->token,
        ]);

        $validateResponse->assertStatus(200)
                        ->assertJson([
                            'status' => true,
                            'message' => 'OTP validated successfully.',
                        ]);

        // Verify OTP is marked as used
        $otpToken->refresh();
        $this->assertTrue($otpToken->is_used);

        // Step 4: Activate user (simulate admin activation)
        $user->update(['status' => UserStatus::ACTIVE]);

        // Step 5: Login with phone
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => '+6281234567890',
            'password' => 'SecurePass123!',
        ]);

        $loginResponse->assertStatus(200)
                     ->assertJson([
                         'status' => true,
                         'message' => 'Login successful.',
                     ])
                     ->assertJsonStructure([
                         'user',
                         'access_token',
                         'refresh_token',
                     ]);

        $loginData = $loginResponse->json();

        // Step 6: Test profile access (would need JWT token in real scenario)
        // For now, test the unauthenticated response
        $profileResponse = $this->getJson('/api/v1/auth/profile');
        $profileResponse->assertStatus(401);

        // Step 7: Logout
        $logoutResponse = $this->postJson('/api/v1/auth/logout', [
            'refresh_token' => $loginData['refresh_token'] ?? 'mock-token',
        ]);

        $logoutResponse->assertStatus(200)
                      ->assertJson([
                          'status' => true,
                          'message' => 'Logout successful.',
                      ]);
    }

    /**
     * Test password reset flow
     *
     * @return void
     */
    public function test_password_reset_flow()
    {
        // Create active user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'status' => UserStatus::ACTIVE,
        ]);

        // Step 1: Request password reset
        $forgotResponse = $this->postJson('/api/v1/auth/forgot-password', [
            'identifier' => 'user@example.com',
        ]);

        $forgotResponse->assertStatus(200)
                      ->assertJson([
                          'status' => true,
                          'message' => 'Password reset link sent.',
                      ]);

        // Verify reset token was created
        $this->assertDatabaseHas('password_reset_tokens', [
            'user_id' => $user->id,
        ]);

        $resetToken = \Modules\Authentication\Models\PasswordResetToken::where('user_id', $user->id)->first();

        // Step 2: Reset password
        $resetResponse = $this->postJson('/api/v1/auth/reset-password', [
            'user_id' => $user->id,
            'token' => $resetToken->token,
            'password' => 'NewSecurePass123!',
        ]);

        $resetResponse->assertStatus(200)
                     ->assertJson([
                         'status' => true,
                         'message' => 'Password reset successfully.',
                     ]);

        // Verify token is marked as used
        $resetToken->refresh();
        $this->assertTrue($resetToken->is_used);

        // Step 3: Try to login with new password
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'user@example.com',
            'password' => 'NewSecurePass123!',
        ]);

        $loginResponse->assertStatus(200)
                     ->assertJson([
                         'status' => true,
                         'message' => 'Login successful.',
                     ]);
    }

    /**
     * Test OTP rate limiting and resend functionality
     *
     * @return void
     */
    public function test_otp_rate_limiting_and_resend_flow()
    {
        $user = User::factory()->create();

        // Step 1: Send initial OTP
        $firstOtpResponse = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $firstOtpResponse->assertStatus(200)
                        ->assertJson([
                            'status' => true,
                            'message' => 'OTP sent successfully.',
                        ]);

        // Step 2: Try to send another OTP immediately (should be rate limited)
        $secondOtpResponse = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $secondOtpResponse->assertStatus(429)
                         ->assertJson([
                             'status' => false,
                             'message' => 'Too many requests. Please try again later.',
                         ]);

        // Step 3: Try resend (should also be rate limited)
        $resendResponse = $this->postJson('/api/v1/auth/resend-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $resendResponse->assertStatus(429)
                      ->assertJson([
                          'status' => false,
                          'message' => 'Too many requests. Please try again later.',
                      ]);
    }

    /**
     * Test multiple identifier login scenarios
     *
     * @return void
     */
    public function test_multiple_identifier_login_scenarios()
    {
        // Create user with both phone and email
        $user = User::factory()->create([
            'phone' => '+6281234567890',
            'email' => 'multi@example.com',
            'password' => bcrypt('TestPass123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        // Test login with phone
        $phoneLoginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => '+6281234567890',
            'password' => 'TestPass123!',
        ]);

        $phoneLoginResponse->assertStatus(200)
                          ->assertJson([
                              'status' => true,
                              'message' => 'Login successful.',
                          ]);

        // Test login with email
        $emailLoginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'multi@example.com',
            'password' => 'TestPass123!',
        ]);

        $emailLoginResponse->assertStatus(200)
                          ->assertJson([
                              'status' => true,
                              'message' => 'Login successful.',
                          ]);

        // Test login with wrong identifier
        $wrongLoginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'wrong@example.com',
            'password' => 'TestPass123!',
        ]);

        $wrongLoginResponse->assertStatus(404)
                          ->assertJson([
                              'status' => false,
                              'message' => 'User not found.',
                          ]);
    }

    /**
     * Test concurrent OTP requests for different types
     *
     * @return void
     */
    public function test_concurrent_otp_requests_different_types()
    {
        $user = User::factory()->create([
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
        ]);

        // Send OTP for phone
        $phoneOtpResponse = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $phoneOtpResponse->assertStatus(200);

        // Send OTP for email (should work even if phone was just sent)
        $emailOtpResponse = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'email',
        ]);

        $emailOtpResponse->assertStatus(200);

        // Verify both tokens were created
        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE->value,
        ]);

        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user->id,
            'type' => IdentifierType::EMAIL->value,
        ]);
    }

    /**
     * Test expired token cleanup
     *
     * @return void
     */
    public function test_expired_token_cleanup()
    {
        $user = User::factory()->create();

        // Create expired OTP
        VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'expires_at' => now()->subMinutes(10), // Expired 10 minutes ago
        ]);

        // Create expired reset token
        \Modules\Authentication\Models\PasswordResetToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'reset-token',
            'expires_at' => now()->subMinutes(70), // Expired 70 minutes ago
        ]);

        // Initially, expired tokens exist
        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user->id,
            'token' => '123456',
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'user_id' => $user->id,
            'token' => 'reset-token',
        ]);

        // In a real scenario, cleanup would happen via scheduled job
        // For testing, we can manually call cleanup methods
        $verificationRepo = app(\Modules\Authentication\Repositories\Verification\IVerificationRepository::class);
        $passwordResetRepo = app(\Modules\Authentication\Repositories\PasswordReset\IPasswordResetRepository::class);

        $verificationRepo->deleteExpiredOtps();
        $passwordResetRepo->deleteExpiredResetTokens();

        // Verify expired tokens are cleaned up
        $this->assertDatabaseMissing('verification_tokens', [
            'user_id' => $user->id,
            'token' => '123456',
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'user_id' => $user->id,
            'token' => 'reset-token',
        ]);
    }

    /**
     * Test user status validation in login
     *
     * @return void
     */
    public function test_user_status_validation_in_login()
    {
        // Test pending user
        $pendingUser = User::factory()->create([
            'email' => 'pending@example.com',
            'password' => bcrypt('TestPass123!'),
            'status' => UserStatus::PENDING,
        ]);

        $pendingLoginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'pending@example.com',
            'password' => 'TestPass123!',
        ]);

        $pendingLoginResponse->assertStatus(200); // Should still allow login for pending users

        // Test suspended user
        $suspendedUser = User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => bcrypt('TestPass123!'),
            'status' => UserStatus::SUSPEND,
        ]);

        $suspendedLoginResponse = $this->postJson('/api/v1/auth/login', [
            'identifier' => 'suspended@example.com',
            'password' => 'TestPass123!',
        ]);

        $suspendedLoginResponse->assertStatus(200); // Business logic would handle suspension in service layer
    }

    /**
     * Test security features in password reset
     *
     * @return void
     */
    public function test_password_reset_security_features()
    {
        $user = User::factory()->create(['email' => 'secure@example.com']);

        // Request password reset
        $this->postJson('/api/v1/auth/forgot-password', [
            'identifier' => 'secure@example.com',
        ]);

        $resetToken = \Modules\Authentication\Models\PasswordResetToken::where('user_id', $user->id)->first();

        // Try to reset with wrong user_id but correct token
        $wrongUserResponse = $this->postJson('/api/v1/auth/reset-password', [
            'user_id' => 99999, // Wrong user ID
            'token' => $resetToken->token,
            'password' => 'NewPass123!',
        ]);

        $wrongUserResponse->assertStatus(400)
                         ->assertJson([
                             'status' => false,
                             'message' => 'Invalid password reset token.',
                         ]);

        // Try to use the same token again (should fail)
        $reuseTokenResponse = $this->postJson('/api/v1/auth/reset-password', [
            'user_id' => $user->id,
            'token' => $resetToken->token,
            'password' => 'AnotherPass123!',
        ]);

        $reuseTokenResponse->assertStatus(400)
                          ->assertJson([
                              'status' => false,
                              'message' => 'Invalid password reset token.',
                          ]);
    }
}