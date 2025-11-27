<?php

namespace Modules\Profile\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a merchant profile is not found
 */
class MerchantProfileNotFoundException extends BaseException
{
    /**
     * Create a new MerchantProfileNotFoundException instance
     *
     * @param string $merchantProfileId The merchant profile ID that was not found
     */
    public function __construct(string $merchantProfileId)
    {
        parent::__construct('messages.merchant_profile_not_found', ['id' => $merchantProfileId], 'profile', 404);
    }
}