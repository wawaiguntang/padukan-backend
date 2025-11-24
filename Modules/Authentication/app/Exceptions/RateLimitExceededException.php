<?php

namespace Modules\Authentication\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when rate limit is exceeded
 */
class RateLimitExceededException extends BaseException
{
    /**
     * Create a new RateLimitExceededException instance
     *
     * @param string $messageKey The message key for frontend localization
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'auth.rate_limit.exceeded', array $context = [])
    {
        parent::__construct($messageKey, $context, 'authentication', 429);
    }
}