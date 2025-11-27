<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when user tries to access resources they don't own
 */
class UnauthorizedAccessException extends BaseException
{
    /**
     * Create a new UnauthorizedAccessException instance
     *
     * @param string $resource The resource type being accessed
     */
    public function __construct(string $resource = 'resource')
    {
        parent::__construct('validation.access_denied', ['resource' => $resource], 'profile', 403);
    }
}