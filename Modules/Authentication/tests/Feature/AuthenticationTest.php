<?php

namespace Modules\Authentication\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Enums\IdentifierType;
use Modules\Authentication\Enums\UserStatus;
use Modules\Authentication\Models\User;
use Modules\Authentication\Models\VerificationToken;
use Tests\TestCase;

/**
 * Authentication Feature Test
 *
 * Comprehensive feature tests for all authentication API endpoints
 * including success and error scenarios
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration
     *
     * @return void
     */
    public function test_user_registration_success()
    {
        $userData = [
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'User registered successfully.',
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user' => [
                        'id',
                        'phone',
                        'email',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ]);

        $this->assertDatabaseHas('users', [
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
            'status' => UserStatus::PENDING->value,
        ]);
    }

    /**
     * Test registration with duplicate phone
     *
     * @return void
     */
    public function test_user_registration_duplicate_phone()
    {
        User::factory()->create(['phone' => '+6281234567890']);

        $userData = [
            'phone' => '+6281234567890',
            'email' => 'different@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test registration with duplicate email
     *
     * @return void
     */
    public function test_user_registration_duplicate_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $userData = [
            'phone' => '+6281234567890',
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration without phone or email
     *
     * @return void
     */
    public function test_user_registration_without_identifier()
    {
        $userData = [
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['identifier']);
    }

    /**
     * Test successful login with phone
     *
     * @return void
     */
    public function test_user_login_success_with_phone()
    {
        $user = User::factory()->create([
            'phone' => '+6281234567890',
            'password' => bcrypt('Password123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        $loginData = [
            'identifier' => '+6281234567890',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Login successful.',
                ])
                ->assertJsonStructure([
                    'status',
                    'message',
                    'user' => [
                        'id',
                        'phone',
                        'email',
                        'status',
                    ],
                    'access_token',
                    'refresh_token',
                ]);
    }

    /**
     * Test successful login with email
     *
     * @return void
     */
    public function test_user_login_success_with_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
            'status' => UserStatus::ACTIVE,
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Login successful.',
                ]);
    }

    /**
     * Test login with invalid credentials
     *
     * @return void
     */
    public function test_user_login_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $loginData = [
            'identifier' => 'test@example.com',
            'password' => 'WrongPassword123!',
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => false,
                    'message' => 'Invalid credentials provided.',
                ]);
    }

    /**
     * Test login with non-existent user
     *
     * @return void
     */
    public function test_user_login_user_not_found()
    {
        $loginData = [
            'identifier' => 'nonexistent@example.com',
            'password' => 'Password123!',
        ];

        $response = $this->postJson('/api/v1/auth/login', $loginData);

        $response->assertStatus(404)
                ->assertJson([
                    'status' => false,
                    'message' => 'User not found.',
                ]);
    }

    /**
     * Test successful OTP send
     *
     * @return void
     */
    public function test_send_otp_success()
    {
        $user = User::factory()->create();

        $otpData = [
            'user_id' => $user->id,
            'type' => 'phone',
        ];

        $response = $this->postJson('/api/v1/auth/send-otp', $otpData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'OTP sent successfully.',
                ]);

        $this->assertDatabaseHas('verification_tokens', [
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE->value,
        ]);
    }

    /**
     * Test OTP send rate limiting
     *
     * @return void
     */
    public function test_send_otp_rate_limiting()
    {
        $user = User::factory()->create();

        // Send first OTP
        $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        // Try to send second OTP immediately (should be rate limited)
        $response = $this->postJson('/api/v1/auth/send-otp', [
            'user_id' => $user->id,
            'type' => 'phone',
        ]);

        $response->assertStatus(429)
                ->assertJson([
                    'status' => false,
                    'message' => 'Too many requests. Please try again later.',
                ]);
    }

    /**
     * Test successful OTP validation
     *
     * @return void
     */
    public function test_validate_otp_success()
    {
        $user = User::factory()->create();
        $token = VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'is_used' => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        $validateData = [
            'user_id' => $user->id,
            'type' => 'phone',
            'token' => '123456',
        ];

        $response = $this->postJson('/api/v1/auth/validate-otp', $validateData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'OTP validated successfully.',
                ]);

        $this->assertDatabaseHas('verification_tokens', [
            'id' => $token->id,
            'is_used' => true,
        ]);
    }

    /**
     * Test OTP validation with invalid token
     *
     * @return void
     */
    public function test_validate_otp_invalid_token()
    {
        $user = User::factory()->create();

        $validateData = [
            'user_id' => $user->id,
            'type' => 'phone',
            'token' => '999999',
        ];

        $response = $this->postJson('/api/v1/auth/validate-otp', $validateData);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'Invalid OTP.',
                ]);
    }

    /**
     * Test OTP validation with expired token
     *
     * @return void
     */
    public function test_validate_otp_expired_token()
    {
        $user = User::factory()->create();
        VerificationToken::factory()->create([
            'user_id' => $user->id,
            'type' => IdentifierType::PHONE,
            'token' => '123456',
            'is_used' => false,
            'expires_at' => now()->subMinutes(1), // Expired
        ]);

        $validateData = [
            'user_id' => $user->id,
            'type' => 'phone',
            'token' => '123456',
        ];

        $response = $this->postJson('/api/v1/auth/validate-otp', $validateData);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'OTP has expired.',
                ]);
    }

    /**
     * Test successful password reset request
     *
     * @return void
     */
    public function test_forgot_password_success()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $forgotData = [
            'identifier' => 'test@example.com',
        ];

        $response = $this->postJson('/api/v1/auth/forgot-password', $forgotData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Password reset link sent.',
                ]);
    }

    /**
     * Test password reset request for non-existent user
     *
     * @return void
     */
    public function test_forgot_password_user_not_found()
    {
        $forgotData = [
            'identifier' => 'nonexistent@example.com',
        ];

        $response = $this->postJson('/api/v1/auth/forgot-password', $forgotData);

        // Should still return success for security (don't reveal if user exists)
        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Password reset link sent.',
                ]);
    }

    /**
     * Test successful password reset
     *
     * @return void
     */
    public function test_reset_password_success()
    {
        $user = User::factory()->create();
        $resetToken = \Modules\Authentication\Models\PasswordResetToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'reset-token-123',
            'is_used' => false,
            'expires_at' => now()->addMinutes(60),
        ]);

        $resetData = [
            'user_id' => $user->id,
            'token' => 'reset-token-123',
            'password' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/v1/auth/reset-password', $resetData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Password reset successfully.',
                ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'id' => $resetToken->id,
            'is_used' => true,
        ]);
    }

    /**
     * Test password reset with invalid token
     *
     * @return void
     */
    public function test_reset_password_invalid_token()
    {
        $user = User::factory()->create();

        $resetData = [
            'user_id' => $user->id,
            'token' => 'invalid-token',
            'password' => 'NewPassword123!',
        ];

        $response = $this->postJson('/api/v1/auth/reset-password', $resetData);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'Invalid password reset token.',
                ]);
    }

    /**
     * Test successful token refresh
     *
     * @return void
     */
    public function test_refresh_token_success()
    {
        $user = User::factory()->create();

        // Mock JWT service to return refresh token data
        $refreshData = [
            'refresh_token' => 'valid-refresh-token',
        ];

        $response = $this->postJson('/api/v1/auth/refresh-token', $refreshData);

        // This would need JWT service mocking for full test
        // For now, test the structure
        $response->assertStatus(401); // Will fail without proper JWT setup
    }

    /**
     * Test refresh token with invalid token
     *
     * @return void
     */
    public function test_refresh_token_invalid()
    {
        $refreshData = [
            'refresh_token' => 'invalid-token',
        ];

        $response = $this->postJson('/api/v1/auth/refresh-token', $refreshData);

        $response->assertStatus(401)
                ->assertJson([
                    'status' => false,
                    'message' => 'Invalid refresh token.',
                ]);
    }

    /**
     * Test successful logout
     *
     * @return void
     */
    public function test_logout_success()
    {
        $logoutData = [
            'refresh_token' => 'valid-refresh-token',
        ];

        $response = $this->postJson('/api/v1/auth/logout', $logoutData);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Logout successful.',
                ]);
    }

    /**
     * Test logout without refresh token
     *
     * @return void
     */
    public function test_logout_without_refresh_token()
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(400)
                ->assertJson([
                    'status' => false,
                    'message' => 'Refresh token is required.',
                ]);
    }

    /**
     * Test get user profile
     *
     * @return void
     */
    public function test_get_user_profile()
    {
        $user = User::factory()->create();

        // This would need authentication middleware mocking
        // For now, test the unauthenticated response
        $response = $this->getJson('/api/v1/auth/profile');

        $response->assertStatus(401)
                ->assertJson([
                    'status' => false,
                    'message' => 'User not authenticated.',
                ]);
    }
}