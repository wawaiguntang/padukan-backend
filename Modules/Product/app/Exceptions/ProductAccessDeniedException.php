<?php

namespace Modules\Product\Exceptions;

use Exception;

class ProductAccessDeniedException extends Exception
{
    public function __construct(string $productId, string $merchantId)
    {
        $message = __('product::exceptions.product_access_denied', [
            'id' => $productId,
            'merchant_id' => $merchantId
        ]);

        parent::__construct($message, 403);
    }
}
