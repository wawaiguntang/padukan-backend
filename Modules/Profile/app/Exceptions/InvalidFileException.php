<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when uploaded file is invalid
 */
class InvalidFileException extends BaseException
{
    /**
     * Create a new InvalidFileException instance
     *
     * @param string $reason The reason why the file is invalid
     * @param array $parameters Additional parameters
     */
    public function __construct(string $reason = 'invalid_file', array $parameters = [])
    {
        parent::__construct($reason, $parameters, 'profile', 422);
    }
}