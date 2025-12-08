<?php

namespace Modules\Product\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when category hierarchy operations fail
 */
class CategoryHierarchyException extends BaseException
{
    /**
     * Create a new CategoryHierarchyException instance
     *
     * @param string $messageKey The specific message key
     * @param array $context Additional context data
     */
    public function __construct(string $messageKey = 'hierarchy_violation', array $context = [])
    {
        parent::__construct('exception.category.' . $messageKey, $context, 'product', 422);
    }
}
