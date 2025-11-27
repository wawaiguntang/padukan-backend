<?php

namespace Modules\Profile\Repositories\Customer;

use Modules\Profile\Models\CustomerDocument;
use Modules\Profile\Repositories\Customer\ICustomerDocumentRepository;

class CustomerDocumentRepository implements ICustomerDocumentRepository
{
    public function getByCustomerProfileId(string $customerProfileId)
    {
        return CustomerDocument::where('customer_profile_id', $customerProfileId)->get();
    }

    public function findById(string $id): ?CustomerDocument
    {
        return CustomerDocument::find($id);
    }

    public function create(array $data): CustomerDocument
    {
        return CustomerDocument::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $document = $this->findById($id);

        if (!$document) {
            return false;
        }

        return $document->update($data);
    }

    public function delete(string $id): bool
    {
        $document = $this->findById($id);

        if (!$document) {
            return false;
        }

        return $document->delete();
    }
}