<?php

namespace App\Exceptions;

use Exception;

/**
 * Base Exception for Authentication Module
 *
 * This class provides a base exception with translation support
 * for all authentication-related exceptions.
 */
class BaseException extends Exception
{
    /**
     * The message key for translation
     *
     * @var string
     */
    protected string $messageKey;

    /**
     * The parameters for message translation
     *
     * @var array
     */
    protected array $parameters;

    /**
     * The module name
     *
     * @var string
     */
    protected string $module;

    /**
     * Constructor
     *
     * @param string $messageKey The translation key for the message
     * @param array $parameters Parameters for message translation
     * @param int $code The exception code
     * @param \Throwable|null $previous The previous exception
     */
    public function __construct(string $messageKey, array $parameters = [], ?string $module = null, int $code = 0, ?\Throwable $previous = null)
    {
        $this->messageKey = $messageKey;
        $this->parameters = $parameters;
        $this->module = $module ?? 'authentication';

        $translatedMessage = $this->getMessageTranslate();

        parent::__construct($translatedMessage, $code, $previous);
    }

    /**
     * Get the message key
     *
     * @return string
     */
    public function getMessageKey(): string
    {
        return $this->messageKey;
    }

    /**
     * Get the translation parameters
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get the translated message
     *
     * @return string
     */
    public function getMessageTranslate(): string
    {
        return __($this->module . '::' . $this->messageKey, $this->parameters);
    }
}
