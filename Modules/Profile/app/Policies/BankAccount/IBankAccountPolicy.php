<?php

namespace Modules\Profile\Policies\BankAccount;

interface IBankAccountPolicy
{
    /**
     * Check if merchant can add more bank accounts
     */
    public function canAddBankAccount(string $merchantProfileId): bool;

    /**
     * Get maximum bank accounts per merchant
     */
    public function getMaxAccountsPerMerchant(): int;

    /**
     * Check if bank is allowed
     */
    public function isBankAllowed(string $bankId): bool;

    /**
     * Get allowed banks
     */
    public function getAllowedBanks(): array;

    /**
     * Check if verification is required for bank accounts
     */
    public function isVerificationRequired(): bool;

    /**
     * Check if primary account should be auto-verified
     */
    public function shouldAutoVerifyPrimary(): bool;

    /**
     * Check if primary account is required
     */
    public function isPrimaryRequired(): bool;
}