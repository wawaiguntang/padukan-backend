<?php

namespace Modules\Profile\Services\Customer;

interface ICustomerService
{
    /**
     * Get customer profile with related data
     *
     * @param string $userId
     * @return array
     */
    public function getProfile(string $userId): array;

    /**
     * Update customer profile
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function updateProfile(string $userId, array $data): array;
}