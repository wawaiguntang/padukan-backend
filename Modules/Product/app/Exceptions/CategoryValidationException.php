<?php

namespace Modules\Product\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when category validation fails
 */
class CategoryValidationException extends BaseException
{
    /**
     * Create a new CategoryValidationException instance
     *
     * @param array $errors Validation errors
     * @param array $context Additional context data
     */
    public function __construct(array $errors, array $context = [])
    {
        $context['errors'] = $errors;
        parent::__construct('exception.category.validation_failed', $context, 'product', 422);
    }
}
