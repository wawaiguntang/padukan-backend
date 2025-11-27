<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when an address is not found
 */
class AddressNotFoundException extends BaseException
{
    /**
     * Create a new AddressNotFoundException instance
     *
     * @param string $addressId The address ID that was not found
     */
    public function __construct(string $addressId)
    {
        parent::__construct('validation.address_not_found', ['id' => $addressId], 'profile', 404);
    }
}