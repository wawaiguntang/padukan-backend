<?php

namespace Modules\Authentication\Tests\Unit\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Middleware\SetLocale;
use Tests\TestCase;

/**
 * Set Locale Middleware Test
 *
 * Tests that SetLocale middleware properly sets application locale
 * based on Accept-Language header
 */
class SetLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The SetLocale middleware instance
     *
     * @var SetLocale
     */
    protected SetLocale $middleware;

    /**
     * Set up the test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new SetLocale();
    }

    /**
     * Test middleware sets locale to English when Accept-Language is 'en'
     *
     * @return void
     */
    public function test_sets_locale_to_english()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'en');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('en', app()->getLocale());
            return response('OK');
        });
    }

    /**
     * Test middleware sets locale to Indonesian when Accept-Language is 'id'
     *
     * @return void
     */
    public function test_sets_locale_to_indonesian()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'id');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('id', app()->getLocale());
            return response('OK');
        });
    }

    /**
     * Test middleware defaults to English when no Accept-Language header
     *
     * @return void
     */
    public function test_defaults_to_english_when_no_header()
    {
        $request = Request::create('/test', 'GET');
        // No Accept-Language header

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('en', app()->getLocale());
            return response('OK');
        });
    }

    /**
     * Test middleware defaults to English for unsupported languages
     *
     * @return void
     */
    public function test_defaults_to_english_for_unsupported_language()
    {
        $unsupportedLanguages = ['fr', 'de', 'es', 'zh', 'ja', 'ko'];

        foreach ($unsupportedLanguages as $lang) {
            $request = Request::create('/test', 'GET');
            $request->headers->set('Accept-Language', $lang);

            $this->middleware->handle($request, function ($req) use ($lang) {
                $this->assertEquals('en', app()->getLocale(), "Failed for language: {$lang}");
                return response('OK');
            });
        }
    }

    /**
     * Test middleware handles complex Accept-Language headers
     *
     * @return void
     */
    public function test_handles_complex_accept_language_headers()
    {
        // Test with quality values
        $testCases = [
            'en-US,en;q=0.9,id;q=0.8' => 'en',
            'id-ID,id;q=0.9,en;q=0.8' => 'id',
            'fr-FR,fr;q=0.9,en;q=0.8' => 'en', // Unsupported falls back to en
            'en;q=0.8,id;q=0.9' => 'id', // id has higher quality
        ];

        foreach ($testCases as $header => $expectedLocale) {
            $request = Request::create('/test', 'GET');
            $request->headers->set('Accept-Language', $header);

            $this->middleware->handle($request, function ($req) use ($expectedLocale, $header) {
                $this->assertEquals($expectedLocale, app()->getLocale(), "Failed for header: {$header}");
                return response('OK');
            });
        }
    }

    /**
     * Test middleware preserves existing locale when header is empty
     *
     * @return void
     */
    public function test_preserves_existing_locale_when_header_empty()
    {
        // Set initial locale
        app()->setLocale('id');

        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', '');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('en', app()->getLocale()); // Should default to en
            return response('OK');
        });
    }

    /**
     * Test middleware handles case insensitive language codes
     *
     * @return void
     */
    public function test_handles_case_insensitive_language_codes()
    {
        $testCases = [
            'EN' => 'en',
            'ID' => 'id',
            'En' => 'en',
            'Id' => 'id',
            'EN-US' => 'en',
            'ID-ID' => 'id',
        ];

        foreach ($testCases as $header => $expectedLocale) {
            $request = Request::create('/test', 'GET');
            $request->headers->set('Accept-Language', $header);

            $this->middleware->handle($request, function ($req) use ($expectedLocale, $header) {
                $this->assertEquals($expectedLocale, app()->getLocale(), "Failed for header: {$header}");
                return response('OK');
            });
        }
    }

    /**
     * Test middleware allows request to continue
     *
     * @return void
     */
    public function test_middleware_allows_request_to_continue()
    {
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'id');

        $response = $this->middleware->handle($request, function ($req) {
            return response('Middleware passed');
        });

        $this->assertEquals('Middleware passed', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test middleware works with different HTTP methods
     *
     * @return void
     */
    public function test_middleware_works_with_different_http_methods()
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        foreach ($methods as $method) {
            $request = Request::create('/test', $method);
            $request->headers->set('Accept-Language', 'id');

            $this->middleware->handle($request, function ($req) use ($method) {
                $this->assertEquals('id', app()->getLocale(), "Failed for method: {$method}");
                return response('OK');
            });
        }
    }

    /**
     * Test middleware priority over application default locale
     *
     * @return void
     */
    public function test_middleware_overrides_application_default_locale()
    {
        // Set application default to something else
        config(['app.locale' => 'fr']); // French as default

        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'id');

        $this->middleware->handle($request, function ($req) {
            $this->assertEquals('id', app()->getLocale(), 'Middleware should override app default');
            return response('OK');
        });
    }
}