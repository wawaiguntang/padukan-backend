<?php

namespace Modules\Product\Exceptions;

use App\Exceptions\BaseException;

class ProductTransactionException extends BaseException
{
    public function __construct(string $messageKey = 'transaction_failed', array $parameters = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('exception.' . $messageKey, $parameters, 'product', $code, $previous);
    }
}
