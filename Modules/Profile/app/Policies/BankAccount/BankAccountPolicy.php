<?php

namespace Modules\Profile\Policies\BusinessValidation;

use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class BankAccountPolicy implements IBankAccountPolicy
{
    private IPolicyRepository $policyRepository;
    private array $policySettings;

    public function __construct(IPolicyRepository $policyRepository)
    {
        $this->policyRepository = $policyRepository;
        $this->loadPolicySettings();
    }

    /**
     * Load policy settings from database
     */
    private function loadPolicySettings(): void
    {
        $settings = $this->policyRepository->getSetting('profile.bank_account');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'max_accounts_per_merchant' => 3,
                'require_primary_account' => true,
                'allowed_banks' => [],
                'verification_required' => true,
                'auto_verify_primary' => false,
            ];
        }
    }

    /**
     * Check if merchant can add more bank accounts
     */
    public function canAddBankAccount(string $merchantProfileId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        // For now, assume unlimited if no repository check
        // In real implementation, would check current count
        return true;
    }

    /**
     * Get maximum bank accounts per merchant
     */
    public function getMaxAccountsPerMerchant(): int
    {
        return $this->policySettings['max_accounts_per_merchant'] ?? 3;
    }

    /**
     * Check if bank is allowed
     */
    public function isBankAllowed(string $bankId): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        $allowedBanks = $this->policySettings['allowed_banks'] ?? [];
        return empty($allowedBanks) || in_array($bankId, $allowedBanks);
    }

    /**
     * Get allowed banks
     */
    public function getAllowedBanks(): array
    {
        return $this->policySettings['allowed_banks'] ?? [];
    }

    /**
     * Check if verification is required for bank accounts
     */
    public function isVerificationRequired(): bool
    {
        return $this->policySettings['verification_required'] ?? true;
    }

    /**
     * Check if primary account should be auto-verified
     */
    public function shouldAutoVerifyPrimary(): bool
    {
        return $this->policySettings['auto_verify_primary'] ?? false;
    }

    /**
     * Check if primary account is required
     */
    public function isPrimaryRequired(): bool
    {
        return $this->policySettings['require_primary_account'] ?? true;
    }
}