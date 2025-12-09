<?php

namespace Modules\Product\Exceptions;

use Exception;

class VariantNotFoundException extends Exception
{
    public function __construct(string $variantId)
    {
        $message = __('product::exceptions.variant_not_found', ['id' => $variantId]);

        parent::__construct($message, 404);
    }
}
