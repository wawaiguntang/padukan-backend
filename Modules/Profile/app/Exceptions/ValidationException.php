<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when validation fails
 */
class ValidationException extends BaseException
{
    protected array $errors;

    /**
     * Create a new ValidationException instance
     *
     * @param array $errors Validation errors
     * @param string $messageKey Error message key
     */
    public function __construct(array $errors, string $messageKey = 'validation_failed')
    {
        $this->errors = $errors;
        parent::__construct($messageKey, [], 'profile', 422);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}