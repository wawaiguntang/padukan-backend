<?php

namespace Modules\Product\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(string $productId, string $merchantId = null)
    {
        $message = $merchantId
            ? __('product::exceptions.product_not_found_for_merchant', ['id' => $productId, 'merchant_id' => $merchantId])
            : __('product::exceptions.product_not_found', ['id' => $productId]);

        parent::__construct($message, 404);
    }
}
