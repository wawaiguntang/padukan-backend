<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a bank is not found
 */
class BankNotFoundException extends BaseException
{
    /**
     * Create a new BankNotFoundException instance
     *
     * @param string $bankId The bank ID that was not found
     */
    public function __construct(string $bankId)
    {
        parent::__construct('validation.bank_not_found', ['id' => $bankId], 'profile', 404);
    }
}