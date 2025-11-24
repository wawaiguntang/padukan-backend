<?php

namespace Modules\Authentication\Exceptions;

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
     * Constructor
     *
     * @param string $messageKey The translation key for the message
     * @param array $parameters Parameters for message translation
     * @param int $code The exception code
     * @param \Throwable|null $previous The previous exception
     */
    public function __construct(string $messageKey, array $parameters = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->messageKey = $messageKey;
        $this->parameters = $parameters;

        // Get the translated message
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
        return __('authentication::' . $this->messageKey, $this->parameters);
    }
}