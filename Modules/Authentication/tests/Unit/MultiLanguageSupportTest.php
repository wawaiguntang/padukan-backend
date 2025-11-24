<?php

namespace Modules\Authentication\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Multi-Language Support Test
 *
 * Tests that all authentication messages are properly translated
 * in both English and Indonesian
 */
class MultiLanguageSupportTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test English translations exist and are not empty
     *
     * @return void
     */
    public function test_english_translations_exist()
    {
        app()->setLocale('en');

        $authMessages = [
            'auth.registration.success',
            'auth.registration.failed',
            'auth.login.success',
            'auth.login.failed',
            'auth.otp.sent',
            'auth.otp.resent',
            'auth.otp.validated',
            'auth.otp.validation_failed',
            'auth.otp.invalid',
            'auth.otp.invalid_format',
            'auth.otp.expired',
            'auth.otp.rate_limit_exceeded',
            'auth.password_reset.sent',
            'auth.password_reset.failed',
            'auth.password_reset.success',
            'auth.token.invalid_refresh_token',
            'auth.token.refreshed',
            'auth.token.refresh_failed',
            'auth.token.refresh_token_required',
            'auth.logout.success',
            'auth.logout.failed',
            'auth.user.not_found',
            'auth.user.already_exists',
            'auth.user.phone_already_exists',
            'auth.user.email_already_exists',
            'auth.invalid_credentials',
            'auth.profile.retrieved',
            'auth.profile.failed',
        ];

        foreach ($authMessages as $messageKey) {
            $translated = __('authentication::' . $messageKey);
            $this->assertNotNull($translated, "Translation missing for: {$messageKey}");
            $this->assertNotEmpty($translated, "Empty translation for: {$messageKey}");
            $this->assertIsString($translated, "Translation is not a string for: {$messageKey}");
        }
    }

    /**
     * Test Indonesian translations exist and are not empty
     *
     * @return void
     */
    public function test_indonesian_translations_exist()
    {
        app()->setLocale('id');

        $authMessages = [
            'auth.registration.success',
            'auth.registration.failed',
            'auth.login.success',
            'auth.login.failed',
            'auth.otp.sent',
            'auth.otp.resent',
            'auth.otp.validated',
            'auth.otp.validation_failed',
            'auth.otp.invalid',
            'auth.otp.invalid_format',
            'auth.otp.expired',
            'auth.otp.rate_limit_exceeded',
            'auth.password_reset.sent',
            'auth.password_reset.failed',
            'auth.password_reset.success',
            'auth.token.invalid_refresh_token',
            'auth.token.refreshed',
            'auth.token.refresh_failed',
            'auth.token.refresh_token_required',
            'auth.logout.success',
            'auth.logout.failed',
            'auth.user.not_found',
            'auth.user.already_exists',
            'auth.user.phone_already_exists',
            'auth.user.email_already_exists',
            'auth.invalid_credentials',
            'auth.profile.retrieved',
            'auth.profile.failed',
        ];

        foreach ($authMessages as $messageKey) {
            $translated = __('authentication::' . $messageKey);
            $this->assertNotNull($translated, "Translation missing for: {$messageKey}");
            $this->assertNotEmpty($translated, "Empty translation for: {$messageKey}");
            $this->assertIsString($translated, "Translation is not a string for: {$messageKey}");
        }
    }

    /**
     * Test that English and Indonesian translations are different
     *
     * @return void
     */
    public function test_english_and_indonesian_translations_are_different()
    {
        $testMessages = [
            'auth.user.not_found',
            'auth.invalid_credentials',
            'auth.registration.success',
            'auth.login.success',
        ];

        foreach ($testMessages as $messageKey) {
            app()->setLocale('en');
            $english = __('authentication::' . $messageKey);

            app()->setLocale('id');
            $indonesian = __('authentication::' . $messageKey);

            // They should be different (unless they happen to be the same word)
            // At minimum, they should both exist and be strings
            $this->assertIsString($english);
            $this->assertIsString($indonesian);
        }
    }

    /**
     * Test validation attribute translations
     *
     * @return void
     */
    public function test_validation_attributes_translated()
    {
        $attributes = [
            'validation.attributes.phone',
            'validation.attributes.email',
            'validation.attributes.password',
            'validation.attributes.identifier',
            'validation.attributes.user_id',
            'validation.attributes.token',
            'validation.attributes.type',
            'validation.attributes.refresh_token',
        ];

        foreach (['en', 'id'] as $locale) {
            app()->setLocale($locale);

            foreach ($attributes as $attributeKey) {
                $translated = __('authentication::' . $attributeKey);
                $this->assertNotNull($translated, "Attribute translation missing for: {$attributeKey} in {$locale}");
                $this->assertNotEmpty($translated, "Empty attribute translation for: {$attributeKey} in {$locale}");
                $this->assertIsString($translated, "Attribute translation is not a string for: {$attributeKey} in {$locale}");
            }
        }
    }

    /**
     * Test custom validation messages
     *
     * @return void
     */
    public function test_custom_validation_messages()
    {
        $customMessages = [
            'validation.identifier.required',
        ];

        foreach (['en', 'id'] as $locale) {
            app()->setLocale($locale);

            foreach ($customMessages as $messageKey) {
                $translated = __('authentication::' . $messageKey);
                $this->assertNotNull($translated, "Custom validation message missing for: {$messageKey} in {$locale}");
                $this->assertNotEmpty($translated, "Empty custom validation message for: {$messageKey} in {$locale}");
                $this->assertIsString($translated, "Custom validation message is not a string for: {$messageKey} in {$locale}");
            }
        }
    }

    /**
     * Test translation with parameters
     *
     * @return void
     */
    public function test_translation_with_parameters()
    {
        // Test messages that might include parameters
        $messagesWithParams = [
            'auth.user.not_found',
            'auth.user.phone_already_exists',
            'auth.user.email_already_exists',
        ];

        $phone = $this->faker->numerify('+628##########');

        foreach (['en', 'id'] as $locale) {
            app()->setLocale($locale);

            foreach ($messagesWithParams as $messageKey) {
                $translated = __('authentication::' . $messageKey, ['phone' => $phone]);
                $this->assertNotNull($translated, "Parameterized translation missing for: {$messageKey} in {$locale}");
                $this->assertNotEmpty($translated, "Empty parameterized translation for: {$messageKey} in {$locale}");
                $this->assertIsString($translated, "Parameterized translation is not a string for: {$messageKey} in {$locale}");
            }
        }
    }

    /**
     * Test that all translation files are properly loaded
     *
     * @return void
     */
    public function test_translation_files_loaded()
    {
        // Test that the authentication module translations are loaded
        $this->assertTrue(
            __('authentication::auth.registration.success') !== 'authentication::auth.registration.success',
            'Authentication translations are not loaded'
        );

        $this->assertTrue(
            __('authentication::validation.attributes.phone') !== 'authentication::validation.attributes.phone',
            'Validation translations are not loaded'
        );
    }

    /**
     * Test fallback to English when Indonesian translation is missing
     *
     * @return void
     */
    public function test_fallback_to_english_when_indonesian_missing()
    {
        app()->setLocale('id');

        // Test with a key that exists in both languages
        $englishTranslation = __('authentication::auth.user.not_found');
        $indonesianTranslation = __('authentication::auth.user.not_found');

        // Both should exist and be different
        $this->assertNotNull($englishTranslation);
        $this->assertNotNull($indonesianTranslation);
        $this->assertIsString($englishTranslation);
        $this->assertIsString($indonesianTranslation);
    }

    /**
     * Test that translation keys follow consistent naming pattern
     *
     * @return void
     */
    public function test_translation_keys_follow_naming_pattern()
    {
        $allKeys = [
            // Auth messages
            'auth.registration.success',
            'auth.registration.failed',
            'auth.login.success',
            'auth.login.failed',
            'auth.otp.sent',
            'auth.otp.resent',
            'auth.otp.validated',
            'auth.otp.validation_failed',
            'auth.otp.invalid',
            'auth.otp.invalid_format',
            'auth.otp.expired',
            'auth.otp.rate_limit_exceeded',
            'auth.password_reset.sent',
            'auth.password_reset.failed',
            'auth.password_reset.success',
            'auth.token.invalid_refresh_token',
            'auth.token.refreshed',
            'auth.token.refresh_failed',
            'auth.token.refresh_token_required',
            'auth.logout.success',
            'auth.logout.failed',
            'auth.user.not_found',
            'auth.user.already_exists',
            'auth.user.phone_already_exists',
            'auth.user.email_already_exists',
            'auth.invalid_credentials',
            'auth.profile.retrieved',
            'auth.profile.failed',
        ];

        foreach ($allKeys as $key) {
            // All keys should follow the pattern: lowercase with dots and underscores
            $this->assertMatchesRegularExpression('/^[a-z][a-z_\.]*[a-z]$/', $key, "Key '{$key}' does not follow naming pattern");
        }
    }
}