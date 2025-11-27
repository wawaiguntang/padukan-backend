<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a customer profile is not found
 */
class CustomerProfileNotFoundException extends BaseException
{
    /**
     * Create a new CustomerProfileNotFoundException instance
     *
     * @param string $customerProfileId The customer profile ID that was not found
     */
    public function __construct(string $customerProfileId)
    {
        parent::__construct('messages.customer_profile_not_found', ['id' => $customerProfileId], 'profile', 404);
    }
}