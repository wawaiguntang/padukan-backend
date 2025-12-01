<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Set Locale Middleware
 *
 * Sets the application locale based on Accept-Language header
 * Supports 'en' and 'id' languages
 */
class SetLocale
{
    /**
     * Supported locales
     *
     * @var array<string>
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Default locale
     *
     * @var string
     */
    protected string $defaultLocale = 'en';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $this->determineLocale($request);

        app()->setLocale($locale);

        // Set locale for translator
        \Illuminate\Support\Facades\App::setLocale($locale);

        return $next($request);
    }

    /**
     * Determine the locale from the request
     *
     * @param Request $request
     * @return string
     */
    protected function determineLocale(Request $request): string
    {
        // Check Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');

        if ($acceptLanguage) {
            // Parse Accept-Language header (e.g., "en;q=0.8,id;q=0.9")
            $locales = explode(',', $acceptLanguage);

            $parsedLocales = [];
            foreach ($locales as $locale) {
                $locale = trim($locale);
                $parts = explode(';', $locale);
                $lang = trim($parts[0]);
                $quality = 1.0; // Default quality

                if (isset($parts[1]) && str_starts_with($parts[1], 'q=')) {
                    $quality = (float) substr($parts[1], 2);
                }

                $parsedLocales[] = ['lang' => $lang, 'quality' => $quality];
            }

            // Sort by quality descending
            usort($parsedLocales, function ($a, $b) {
                return $b['quality'] <=> $a['quality'];
            });

            foreach ($parsedLocales as $parsed) {
                $locale = strtolower($parsed['lang']);

                // Check if locale is supported
                if (in_array($locale, $this->supportedLocales)) {
                    return $locale;
                }

                // Check language prefix (e.g., "id-ID" -> "id")
                $language = explode('-', $locale)[0];
                if (in_array($language, $this->supportedLocales)) {
                    return $language;
                }
            }
        }

        // Check query parameter ?lang=en
        $queryLocale = $request->query('lang');
        if ($queryLocale && in_array($queryLocale, $this->supportedLocales)) {
            return $queryLocale;
        }

        // Check session
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, $this->supportedLocales)) {
            return $sessionLocale;
        }

        return $this->defaultLocale;
    }
}
