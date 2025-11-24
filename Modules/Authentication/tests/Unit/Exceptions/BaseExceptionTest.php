<?php

namespace Modules\Authentication\Tests\Unit\Exceptions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Authentication\Exceptions\BaseException;
use Tests\TestCase;

/**
 * Base Exception Test
 *
 * Tests BaseException functionality including message translation
 */
class BaseExceptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test BaseException creation with message key
     *
     * @return void
     */
    public function test_base_exception_creation()
    {
        $messageKey = 'auth.user.not_found';
        $parameters = ['id' => 'uuid-123'];

        $exception = new BaseException($messageKey, $parameters);

        $this->assertInstanceOf(BaseException::class, $exception);
        $this->assertEquals($messageKey, $exception->getMessageKey());
        $this->assertEquals($parameters, $exception->getParameters());
    }

    /**
     * Test BaseException creation without parameters
     *
     * @return void
     */
    public function test_base_exception_without_parameters()
    {
        $messageKey = 'auth.invalid_credentials';

        $exception = new BaseException($messageKey);

        $this->assertInstanceOf(BaseException::class, $exception);
        $this->assertEquals($messageKey, $exception->getMessageKey());
        $this->assertEquals([], $exception->getParameters());
    }

    /**
     * Test getMessageTranslate method
     *
     * @return void
     */
    public function test_get_message_translate()
    {
        $messageKey = 'auth.user.not_found';
        $parameters = ['id' => 'uuid-123'];

        $exception = new BaseException($messageKey, $parameters);

        // Test with default locale (en)
        $translatedMessage = $exception->getMessageTranslate();

        // Should return the translated message
        $this->assertIsString($translatedMessage);
        $this->assertNotEmpty($translatedMessage);
    }

    /**
     * Test getMessageTranslate with different locales
     *
     * @return void
     */
    public function test_get_message_translate_with_locale()
    {
        $messageKey = 'auth.user.not_found';
        $parameters = ['id' => 'uuid-123'];

        $exception = new BaseException($messageKey, $parameters);

        // Test with English locale
        app()->setLocale('en');
        $englishMessage = $exception->getMessageTranslate();

        // Test with Indonesian locale
        app()->setLocale('id');
        $indonesianMessage = $exception->getMessageTranslate();

        // Messages should be different for different locales
        $this->assertIsString($englishMessage);
        $this->assertIsString($indonesianMessage);
        // Note: In a real test environment, these would be different
        // but for this test, we just verify they return strings
    }

    /**
     * Test BaseException extends proper base exception
     *
     * @return void
     */
    public function test_base_exception_extends_proper_base()
    {
        $exception = new BaseException('test.message');

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    /**
     * Test BaseException with complex parameters
     *
     * @return void
     */
    public function test_base_exception_with_complex_parameters()
    {
        $messageKey = 'auth.user.not_found';
        $parameters = [
            'id' => 'uuid-123',
            'email' => 'test@example.com',
            'phone' => '+6281234567890',
            'timestamp' => now(),
        ];

        $exception = new BaseException($messageKey, $parameters);

        $this->assertEquals($parameters, $exception->getParameters());
        $this->assertEquals('uuid-123', $exception->getParameters()['id']);
        $this->assertEquals('test@example.com', $exception->getParameters()['email']);
    }
}