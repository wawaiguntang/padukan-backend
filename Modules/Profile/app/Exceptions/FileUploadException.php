<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when file upload fails
 */
class FileUploadException extends BaseException
{
    /**
     * Create a new FileUploadException instance
     *
     * @param string $message The error message
     * @param array $parameters Additional parameters
     */
    public function __construct(string $message = 'file_upload_failed', array $parameters = [])
    {
        parent::__construct($message, $parameters, 'profile', 422);
    }
}