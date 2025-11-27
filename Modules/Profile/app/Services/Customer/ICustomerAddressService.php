<?php

namespace Modules\Profile\Services\Customer;

interface ICustomerAddressService
{
    /**
     * Get all addresses for a customer
     *
     * @param string $userId
     * @return array
     */
    public function getAddresses(string $userId): array;

    /**
     * Get specific address for a customer
     *
     * @param string $userId
     * @param string $addressId
     * @return array
     */
    public function getAddress(string $userId, string $addressId): array;

    /**
     * Create new address for customer
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function createAddress(string $userId, array $data): array;

    /**
     * Update address for customer
     *
     * @param string $userId
     * @param string $addressId
     * @param array $data
     * @return array
     */
    public function updateAddress(string $userId, string $addressId, array $data): array;

    /**
     * Delete address for customer
     *
     * @param string $userId
     * @param string $addressId
     * @return bool
     */
    public function deleteAddress(string $userId, string $addressId): bool;
}