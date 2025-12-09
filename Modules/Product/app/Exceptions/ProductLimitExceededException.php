<?php

namespace Modules\Product\Exceptions;

use Exception;

class ProductLimitExceededException extends Exception
{
    protected array $limitInfo;

    public function __construct(array $limitInfo, string $merchantId)
    {
        $this->limitInfo = $limitInfo;

        $message = __('product::exceptions.product_limit_exceeded', [
            'merchant_id' => $merchantId,
            'current' => $limitInfo['current_count'] ?? 0,
            'max' => $limitInfo['max_allowed'] ?? 0
        ]);

        parent::__construct($message, 429);
    }

    public function getLimitInfo(): array
    {
        return $this->limitInfo;
    }
}
