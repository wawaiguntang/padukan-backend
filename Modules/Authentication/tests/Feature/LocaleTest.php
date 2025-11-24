<?php

namespace Modules\Authentication\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Locale Test
 *
 * Tests for locale middleware and translations
 */
class LocaleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test default locale is English
     *
     * @return void
     */
    public function test_default_locale_is_english()
    {
        // Act
        $response = $this->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('en', app()->getLocale());
    }

    /**
     * Test Accept-Language header sets Indonesian locale
     *
     * @return void
     */
    public function test_accept_language_sets_indonesian_locale()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'id',
        ])->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('id', app()->getLocale());
    }

    /**
     * Test Accept-Language header sets English locale
     *
     * @return void
     */
    public function test_accept_language_sets_english_locale()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('en', app()->getLocale());
    }

    /**
     * Test Accept-Language with quality value
     *
     * @return void
     */
    public function test_accept_language_with_quality_value()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'id,en;q=0.9',
        ])->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('id', app()->getLocale());
    }

    /**
     * Test Accept-Language with language prefix
     *
     * @return void
     */
    public function test_accept_language_with_language_prefix()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'id-ID,en-US;q=0.9',
        ])->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('id', app()->getLocale());
    }

    /**
     * Test unsupported language falls back to English
     *
     * @return void
     */
    public function test_unsupported_language_falls_back_to_english()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'fr,de',
        ])->get('/api/v1/auth/test-locale');

        // Assert
        $this->assertEquals('en', app()->getLocale());
    }

    /**
     * Test query parameter lang sets locale
     *
     * @return void
     */
    public function test_query_parameter_sets_locale()
    {
        // Act
        $response = $this->get('/api/v1/auth/test-locale?lang=id');

        // Assert
        $this->assertEquals('id', app()->getLocale());
    }

    /**
     * Test validation messages are translated
     *
     * @return void
     */
    public function test_validation_messages_are_translated_to_indonesian()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'id',
        ])->postJson('/api/v1/auth/register', [
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email' => [],
                'password' => [],
            ]
        ]);

        // Check if Indonesian messages are used
        $responseData = $response->json();
        $this->assertTrue(str_contains($responseData['errors']['email'][0] ?? '', 'wajib diisi'));
    }

    /**
     * Test validation messages are translated to English
     *
     * @return void
     */
    public function test_validation_messages_are_translated_to_english()
    {
        // Act
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
        ])->postJson('/api/v1/auth/register', [
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'email' => [],
                'password' => [],
            ]
        ]);

        // Check if English messages are used
        $responseData = $response->json();
        $this->assertTrue(str_contains($responseData['errors']['email'][0] ?? '', 'valid email'));
    }
}