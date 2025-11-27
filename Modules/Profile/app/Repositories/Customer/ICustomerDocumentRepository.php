<?php

namespace Modules\Profile\Repositories\Customer;

use Modules\Profile\Models\CustomerDocument;

interface ICustomerDocumentRepository
{
    /**
     * Get all documents by customer profile ID
     *
     * @param string $customerProfileId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCustomerProfileId(string $customerProfileId);

    /**
     * Find document by ID
     *
     * @param string $id
     * @return CustomerDocument|null
     */
    public function findById(string $id);

    /**
     * Create new document
     *
     * @param array $data
     * @return CustomerDocument
     */
    public function create(array $data): CustomerDocument;

    /**
     * Update document
     *
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update(string $id, array $data): bool;

    /**
     * Delete document
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool;
}