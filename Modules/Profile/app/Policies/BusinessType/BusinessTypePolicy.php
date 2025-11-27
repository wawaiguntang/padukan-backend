<?php

namespace Modules\Profile\Policies\BusinessValidation;

use Modules\Authorization\Repositories\Policy\IPolicyRepository;

class BusinessTypePolicy implements IBusinessTypePolicy
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
        $settings = $this->policyRepository->getSetting('profile.business_type');

        if ($settings) {
            $this->policySettings = $settings;
        } else {
            // Fallback to default
            $this->policySettings = [
                'enabled' => true,
                'allowed_types' => ['food', 'mart'],
                'require_business_type' => true,
                'auto_validate' => true,
                'custom_validation_rules' => [],
            ];
        }
    }

    /**
     * Validate business type
     */
    public function validateBusinessType(string $businessType): bool
    {
        if (!$this->policySettings['enabled']) {
            return true;
        }

        return in_array($businessType, $this->policySettings['allowed_types']);
    }

    /**
     * Get allowed business types
     */
    public function getAllowedTypes(): array
    {
        return $this->policySettings['allowed_types'] ?? ['food', 'mart'];
    }

    /**
     * Check if business type validation is required
     */
    public function isRequired(): bool
    {
        return $this->policySettings['require_business_type'] ?? true;
    }

    /**
     * Check if auto validation should be performed
     */
    public function shouldAutoValidate(): bool
    {
        return $this->policySettings['auto_validate'] ?? true;
    }
}