<?php

namespace App\Exceptions;

use Exception;

/**
 * Base Exception Class
 *
 * Provides common functionality for all application exceptions
 * including automatic message translation based on current locale.
 */
class BaseException extends Exception
{
    /**
     * Additional context data
     *
     * @var array
     */
    protected array $context;

    /**
     * Module name for translation namespace
     *
     * @var string
     */
    protected string $module;

    /**
     * Create a new BaseException instance
     *
     * @param string $messageKey The message key for translation
     * @param array $context Additional context data
     * @param string $module Module name for translation namespace (default: 'app')
     * @param int $code HTTP status code
     */
    public function __construct(string $messageKey, array $context = [], string $module = 'app', int $code = 400)
    {
        parent::__construct($messageKey, $code);
        $this->context = $context;
        $this->module = $module;
    }

    /**
     * Get the translated message
     *
     * @return string The translated message
     */
    public function getMessageTranslate(): string
    {
        $translationKey = $this->module === 'app'
            ? $this->getMessage()
            : $this->module . '::' . $this->getMessage();

        return __($translationKey, $this->context);
    }

    /**
     * Get the context data
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get the module name
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }
}