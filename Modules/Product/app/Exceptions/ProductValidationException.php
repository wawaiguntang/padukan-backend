<?php

namespace Modules\Product\Exceptions;

use Exception;

class ProductValidationException extends Exception
{
    protected array $errors;

    public function __construct(array $errors, string $message = null)
    {
        $this->errors = $errors;
        $message = $message ?? __('product::exceptions.validation_failed');

        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
