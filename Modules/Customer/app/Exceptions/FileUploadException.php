<?php

namespace Modules\Customer\Exceptions;

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
    public function __construct(string $messageKey = 'customer.file.upload_failed', array $context = [])
    {
        parent::__construct($messageKey, $context, 'customer', 500);
    }
}
