<?php

namespace Modules\Customer\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when file validation fails
 */
class FileValidationException extends BaseException
{
    /**
     * Create a new FileValidationException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'exception.file.validation_failed', array $context = [])
    {
        parent::__construct($messageKey, $context, 'customer', 422);
    }
}
