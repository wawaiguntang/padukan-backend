<?php

namespace Modules\Customer\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a document is not found
 */
class DocumentNotFoundException extends BaseException
{
    /**
     * Create a new DocumentNotFoundException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'exception.document.not_found', array $context = [])
    {
        parent::__construct($messageKey, $context, 'customer', 404);
    }
}
