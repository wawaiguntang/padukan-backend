<?php

namespace Modules\Driver\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when file upload fails
 */
class FileUploadException extends BaseException
{
    /**
     * Create a new FileUploadException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'driver.file.upload_failed', array $context = [])
    {
        parent::__construct($messageKey, $context, 'driver', 500);
    }
}
