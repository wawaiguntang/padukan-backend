<?php

namespace Modules\Profile\Services\Bank;

use Modules\Profile\Models\Bank;
use Modules\Profile\Repositories\Bank\IBankRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Bank Service Implementation
 *
 * Handles bank data management business logic
 */
class BankService implements IBankService
{
    protected IBankRepository $bankRepository;

    public function __construct(IBankRepository $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function getActiveBanks(): Collection
    {
        try {
            return $this->bankRepository->getActiveBanks();
        } catch (\Exception $e) {
            Log::error('Failed to get active banks', [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to retrieve active banks');
        }
    }

    public function getBankById(string $bankId): ?Bank
    {
        try {
            return $this->bankRepository->findById($bankId);
        } catch (\Exception $e) {
            Log::error('Failed to get bank by ID', [
                'bank_id' => $bankId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getBankByCode(string $code): ?Bank
    {
        try {
            return $this->bankRepository->findByCode($code);
        } catch (\Exception $e) {
            Log::error('Failed to get bank by code', [
                'code' => $code,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function createBank(array $data): Bank
    {
        try {
            // Validate data
            $this->validateBankData($data);

            // Check code uniqueness
            if (!$this->isBankCodeUnique($data['code'])) {
                throw new \Exception('Bank code already exists');
            }

            return $this->bankRepository->create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create bank', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to create bank');
        }
    }

    public function updateBank(string $bankId, array $data): bool
    {
        try {
            $bank = $this->bankRepository->findById($bankId);
            if (!$bank) {
                throw new \Exception('Bank not found');
            }

            // Validate data
            $this->validateBankData($data, false);

            // Check code uniqueness if code is being changed
            if (isset($data['code']) && $data['code'] !== $bank->code) {
                if (!$this->isBankCodeUnique($data['code'], $bankId)) {
                    throw new \Exception('Bank code already exists');
                }
            }

            return $this->bankRepository->update($bankId, $data);
        } catch (\Exception $e) {
            Log::error('Failed to update bank', [
                'bank_id' => $bankId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to update bank');
        }
    }

    public function deactivateBank(string $bankId): bool
    {
        try {
            $bank = $this->bankRepository->findById($bankId);
            if (!$bank) {
                throw new \Exception('Bank not found');
            }

            if (!$bank->is_active) {
                throw new \Exception('Bank is already inactive');
            }

            return $this->bankRepository->update($bankId, ['is_active' => false]);
        } catch (\Exception $e) {
            Log::error('Failed to deactivate bank', [
                'bank_id' => $bankId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to deactivate bank');
        }
    }

    public function isBankCodeUnique(string $code, ?string $excludeId = null): bool
    {
        try {
            $existingBank = $this->bankRepository->findByCode($code);

            if (!$existingBank) {
                return true;
            }

            // If excludeId is provided, check if it's the same bank
            if ($excludeId && $existingBank->id === $excludeId) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to check bank code uniqueness', [
                'code' => $code,
                'exclude_id' => $excludeId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate bank data
     */
    protected function validateBankData(array $data, bool $isCreate = true): void
    {
        // Required fields
        if (empty($data['name'])) {
            throw new \Exception('Bank name is required');
        }

        if (empty($data['code'])) {
            throw new \Exception('Bank code is required');
        }

        // Validate code format (alphanumeric, uppercase)
        if (!preg_match('/^[A-Z0-9]+$/', $data['code'])) {
            throw new \Exception('Bank code must be alphanumeric and uppercase');
        }

        // Validate name length
        if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            throw new \Exception('Bank name must be between 2 and 100 characters');
        }

        // Validate code length
        if (strlen($data['code']) < 3 || strlen($data['code']) > 10) {
            throw new \Exception('Bank code must be between 3 and 10 characters');
        }
    }
}