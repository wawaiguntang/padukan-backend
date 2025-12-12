<?php

namespace Modules\Tax\Services\Tax;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Modules\Tax\Models\Tax;
use Modules\Tax\Models\TaxGroup;
use Modules\Tax\Models\TaxRate;
use Modules\Tax\Repositories\Tax\ITaxRepository;
use Modules\Tax\Repositories\TaxAssignment\ITaxAssignmentRepository;
use Modules\Tax\Repositories\TaxGroup\ITaxGroupRepository;
use Modules\Tax\Repositories\TaxRate\ITaxRateRepository;

class TaxService implements ITaxService
{
    private ITaxRepository $taxRepository;
    private ITaxGroupRepository $taxGroupRepository;
    private ITaxRateRepository $taxRateRepository;
    private ITaxAssignmentRepository $taxAssignmentRepository;

    public function __construct(
        ITaxRepository $taxRepository,
        ITaxGroupRepository $taxGroupRepository,
        ITaxRateRepository $taxRateRepository,
        ITaxAssignmentRepository $taxAssignmentRepository
    ) {
        $this->taxRepository = $taxRepository;
        $this->taxGroupRepository = $taxGroupRepository;
        $this->taxRateRepository = $taxRateRepository;
        $this->taxAssignmentRepository = $taxAssignmentRepository;
    }

    /**
     * Create a new tax for any owner type.
     */
    public function createTax(string $ownerType, ?string $ownerId, array $data): Tax
    {
        // Validate ownership permission (placeholder - implement based on your auth system)
        $this->validateCreatePermission($ownerType, $ownerId);

        // Set ownership data
        $data['owner_type'] = $ownerType;
        $data['owner_id'] = $ownerId;

        // Create tax
        $tax = $this->taxRepository->create($data);

        return $tax;
    }

    /**
     * Update an existing tax with ownership validation.
     */
    public function updateTax(string $ownerType, ?string $ownerId, string $taxId, array $data): bool
    {
        // Validate ownership
        $this->validateTaxOwnership($taxId, $ownerId, $ownerType);

        // Update tax
        return $this->taxRepository->update($taxId, $data);
    }

    /**
     * Delete a tax with ownership validation.
     */
    public function deleteTax(string $ownerType, ?string $ownerId, string $taxId): bool
    {
        // Validate ownership
        $this->validateTaxOwnership($taxId, $ownerId, $ownerType);

        // Delete tax
        return $this->taxRepository->delete($taxId);
    }

    /**
     * Get taxes by owner type (dynamic query).
     */
    public function getTaxes(string $ownerType, ?string $ownerId = null): Collection
    {
        return $this->taxRepository->findByOwnerType($ownerType, $ownerId);
    }

    /**
     * Create a tax group for any owner type.
     */
    public function createTaxGroup(string $ownerType, ?string $ownerId, array $data): TaxGroup
    {
        // Validate permission
        $this->validateCreatePermission($ownerType, $ownerId);

        // Set ownership
        $data['owner_type'] = $ownerType;
        $data['owner_id'] = $ownerId;

        // Create tax group
        return $this->taxGroupRepository->create($data);
    }

    /**
     * Assign a tax group to context entities (dynamic assignment).
     */
    public function assignTaxToContext(string $taxGroupId, array $context): bool
    {
        // Validate tax group ownership (placeholder)
        $this->validateTaxGroupAccess($taxGroupId);

        // Convert context to assignments
        $assignments = $this->buildAssignmentsFromContext($taxGroupId, $context);

        // Create bulk assignments
        return $this->taxAssignmentRepository->createBulk($taxGroupId, $assignments)->isNotEmpty();
    }

    /**
     * Calculate tax for a given price with owner context.
     */
    public function calculateTaxForOwner(float $price, string $ownerType, ?string $ownerId, array $context = []): array
    {
        // Get effective taxes for this owner hierarchy
        $effectiveTaxes = $this->getEffectiveTaxes($ownerType, $ownerId, $context);

        // Calculate tax directly
        return $this->calculateTaxAmount($price, $effectiveTaxes);
    }

    /**
     * Check if user can manage a specific tax.
     */
    public function canManageTax(string $userId, string $userType, string $ownerType, ?string $ownerId, string $taxId): bool
    {
        // Placeholder permission logic - implement based on your auth system
        return $this->checkPermission($userId, $userType, $ownerType, $ownerId, $taxId);
    }

    /**
     * Validate tax ownership dynamically.
     */
    public function validateTaxOwnership(string $taxId, ?string $ownerId, string $ownerType): bool
    {
        return $this->taxRepository->findById($taxId) &&
            $this->checkOwnership($taxId, $ownerId, $ownerType);
    }

    /**
     * Build assignment data from context array.
     *
     * @param string $taxGroupId
     * @param array<string, array<string>> $context
     * @return array<array{type: string, id: string}>
     */
    private function buildAssignmentsFromContext(string $taxGroupId, array $context): array
    {
        $assignments = [];

        foreach ($context as $contextType => $entityIds) {
            // Convert plural to singular (e.g., 'regions' -> 'region')
            $assignableType = Str::singular($contextType);

            foreach ($entityIds as $entityId) {
                $assignments[] = [
                    'type' => $assignableType,
                    'id' => $entityId
                ];
            }
        }

        return $assignments;
    }

    /**
     * Get effective taxes considering hierarchy (System → Organization → Specific Owner).
     *
     * @param string $ownerType
     * @param string|null $ownerId
     * @param array<string, array<string>> $context
     * @return array Tax data for calculation
     */
    private function getEffectiveTaxes(string $ownerType, ?string $ownerId, array $context): array
    {
        $taxes = [];

        // 1. Always include system/global taxes
        $systemTaxes = $this->taxRepository->findByOwnerType('system');
        $taxes = array_merge($taxes, $this->getApplicableTaxes($systemTaxes, $context));

        // 2. Include owner-specific taxes if owner_id provided
        if ($ownerId) {
            $ownerTaxes = $this->taxRepository->findByOwnerType($ownerType, $ownerId);
            $taxes = array_merge($taxes, $this->getApplicableTaxes($ownerTaxes, $context));
        }

        // 3. Apply priority and remove duplicates
        return $this->resolveTaxHierarchy($taxes);
    }

    /**
     * Get applicable taxes from a collection based on context.
     *
     * @param Collection<Tax> $taxes
     * @param array<string, array<string>> $context
     * @return array
     */
    private function getApplicableTaxes(Collection $taxes, array $context): array
    {
        $applicableTaxes = [];

        foreach ($taxes as $tax) {
            // Get tax groups for this tax
            $taxGroups = $this->taxGroupRepository->findByAssignedEntity('tax', $tax->id);

            foreach ($taxGroups as $taxGroup) {
                // Check if tax group applies to context
                if ($this->taxGroupAppliesToContext($taxGroup, $context)) {
                    // Get active rates for this tax group
                    $rates = $this->taxRateRepository->findActiveByGroup($taxGroup->id);

                    foreach ($rates as $rate) {
                        $applicableTaxes[] = [
                            'id' => $rate->id,
                            'name' => $tax->name,
                            'rate' => $rate->rate,
                            'type' => $rate->type,
                            'is_inclusive' => $rate->is_inclusive,
                            'priority' => $rate->priority,
                            'based_on' => $rate->based_on,
                        ];
                    }
                }
            }
        }

        return $applicableTaxes;
    }

    /**
     * Check if tax group applies to given context.
     *
     * @param TaxGroup $taxGroup
     * @param array<string, array<string>> $context
     * @return bool
     */
    private function taxGroupAppliesToContext(TaxGroup $taxGroup, array $context): bool
    {
        $assignments = $taxGroup->assignments;

        // If no assignments, apply globally
        if ($assignments->isEmpty()) {
            return true;
        }

        // Check if any assignment matches context
        foreach ($context as $contextType => $contextIds) {
            $assignableType = Str::singular($contextType);

            $matchingAssignments = $assignments->where('assignable_type', $assignableType)
                ->whereIn('assignable_id', $contextIds);

            if ($matchingAssignments->isNotEmpty()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve tax hierarchy by priority and remove duplicates.
     *
     * @param array $taxes
     * @return array
     */
    private function resolveTaxHierarchy(array $taxes): array
    {
        // Group by priority
        $groupedByPriority = collect($taxes)->groupBy('priority');

        $resolvedTaxes = [];

        foreach ($groupedByPriority as $priority => $priorityTaxes) {
            // For same priority, take the first one (could be enhanced with conflict resolution)
            $resolvedTaxes[] = $priorityTaxes->first();
        }

        // Sort by priority (ascending)
        return collect($resolvedTaxes)->sortBy('priority')->values()->all();
    }

    /**
     * Validate create permission (placeholder).
     */
    private function validateCreatePermission(string $ownerType, ?string $ownerId): void
    {
        // Implement based on your authentication/authorization system
        // For now, allow all (implement proper checks)
    }

    /**
     * Validate tax group access (placeholder).
     */
    private function validateTaxGroupAccess(string $taxGroupId): void
    {
        // Implement ownership validation for tax groups
    }

    /**
     * Check permission (placeholder).
     */
    private function checkPermission(string $userId, string $userType, string $ownerType, ?string $ownerId, string $taxId): bool
    {
        // Implement proper permission checking based on your auth system
        // For now, return true (implement proper logic)
        return true;
    }

    /**
     * Check ownership (placeholder).
     */
    private function checkOwnership(string $taxId, ?string $ownerId, string $ownerType): bool
    {
        $tax = $this->taxRepository->findById($taxId);

        if (!$tax) {
            return false;
        }

        if ($tax->owner_type !== $ownerType) {
            return false;
        }

        // For system taxes, owner_id must be null
        if ($ownerType === 'system') {
            return $tax->owner_id === null;
        }

        // For other types, owner_id must match
        return $tax->owner_id === $ownerId;
    }

    /**
     * Calculate tax amount from effective taxes.
     *
     * @param float $price
     * @param array $effectiveTaxes
     * @return array
     */
    private function calculateTaxAmount(float $price, array $effectiveTaxes): array
    {
        $totalTax = 0;
        $taxes = [];
        $runningTotal = $price;

        // Sort taxes by priority
        usort($effectiveTaxes, fn($a, $b) => $a['priority'] <=> $b['priority']);

        foreach ($effectiveTaxes as $tax) {
            $taxAmount = 0;

            // Determine calculation base
            $calculationBase = ($tax['based_on'] === 'total_after_previous_tax')
                ? $runningTotal
                : $price;

            // Calculate tax based on type
            if ($tax['type'] === 'percentage') {
                if ($tax['is_inclusive']) {
                    // Reverse calculation for inclusive tax
                    $base = $calculationBase / (1 + ($tax['rate'] / 100));
                    $taxAmount = $calculationBase - $base;
                } else {
                    $taxAmount = $calculationBase * ($tax['rate'] / 100);
                }
            } else {
                // Fixed amount
                $taxAmount = $tax['rate'];
            }

            $taxes[] = [
                'name' => $tax['name'],
                'rate' => $tax['rate'],
                'amount' => round($taxAmount, 2),
                'priority' => $tax['priority']
            ];

            // Add to running total if not inclusive
            if (!$tax['is_inclusive']) {
                $totalTax += $taxAmount;
                $runningTotal += $taxAmount;
            }
        }

        return [
            'base_amount' => $price,
            'taxes' => $taxes,
            'total_tax' => round($totalTax, 2),
            'grand_total' => round($price + $totalTax, 2)
        ];
    }

    // ===== TAX GROUP MANAGEMENT =====

    /**
     * Update an existing tax group with ownership validation.
     */
    public function updateTaxGroup(string $ownerType, ?string $ownerId, string $groupId, array $data): bool
    {
        // Validate ownership
        $this->validateTaxGroupOwnership($groupId, $ownerId, $ownerType);

        // Update tax group
        return $this->taxGroupRepository->update($groupId, $data);
    }

    /**
     * Delete a tax group with ownership validation.
     */
    public function deleteTaxGroup(string $ownerType, ?string $ownerId, string $groupId): bool
    {
        // Validate ownership
        $this->validateTaxGroupOwnership($groupId, $ownerId, $ownerType);

        // Delete tax group
        return $this->taxGroupRepository->delete($groupId);
    }

    /**
     * Get tax groups by owner type (dynamic query).
     */
    public function getTaxGroups(string $ownerType, ?string $ownerId = null): Collection
    {
        return $this->taxGroupRepository->findByOwnerType($ownerType, $ownerId);
    }

    /**
     * Get tax group by ID with ownership validation.
     */
    public function getTaxGroupById(string $groupId): ?TaxGroup
    {
        return $this->taxGroupRepository->findById($groupId);
    }

    // ===== TAX RATE MANAGEMENT =====

    /**
     * Create a new tax rate for a tax group.
     */
    public function createTaxRate(string $groupId, array $data): TaxRate
    {
        // Validate tax group access (placeholder)
        $this->validateTaxGroupAccess($groupId);

        // Set group ID
        $data['tax_group_id'] = $groupId;

        // Create tax rate
        return $this->taxRateRepository->create($data);
    }

    /**
     * Update an existing tax rate.
     */
    public function updateTaxRate(string $rateId, array $data): bool
    {
        // Update tax rate
        return $this->taxRateRepository->update($rateId, $data);
    }

    /**
     * Delete a tax rate.
     */
    public function deleteTaxRate(string $rateId): bool
    {
        // Delete tax rate
        return $this->taxRateRepository->delete($rateId);
    }

    /**
     * Get tax rates by group ID.
     */
    public function getTaxRatesByGroup(string $groupId): Collection
    {
        return $this->taxRateRepository->findByGroup($groupId);
    }

    /**
     * Get active tax rates by group ID (considering validity dates).
     */
    public function getActiveTaxRatesByGroup(string $groupId): Collection
    {
        return $this->taxRateRepository->findActiveByGroup($groupId);
    }

    // ===== ADVANCED ASSIGNMENT MANAGEMENT =====

    /**
     * Remove tax assignments from context entities.
     */
    public function removeTaxFromContext(string $taxGroupId, array $context): bool
    {
        // Validate tax group access
        $this->validateTaxGroupAccess($taxGroupId);

        // Convert context to assignments for removal
        $assignments = $this->buildAssignmentsFromContext($taxGroupId, $context);

        // Remove assignments
        return $this->taxAssignmentRepository->deleteBulk($taxGroupId, $assignments) > 0;
    }

    /**
     * Get all entities assigned to a tax group.
     */
    public function getAssignedEntities(string $taxGroupId): Collection
    {
        return $this->taxAssignmentRepository->findByGroup($taxGroupId);
    }

    /**
     * Get all tax groups assigned to a specific entity.
     */
    public function getTaxGroupsForEntity(string $entityType, string $entityId): Collection
    {
        return $this->taxGroupRepository->findByAssignedEntity($entityType, $entityId);
    }

    // ===== BULK OPERATIONS =====

    /**
     * Create multiple taxes in bulk for any owner type.
     */
    public function createBulkTaxes(string $ownerType, ?string $ownerId, array $taxesData): Collection
    {
        // Validate permission
        $this->validateCreatePermission($ownerType, $ownerId);

        // Set ownership for all taxes
        $taxesWithOwnership = array_map(function ($taxData) use ($ownerType, $ownerId) {
            return array_merge($taxData, [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
            ]);
        }, $taxesData);

        // Create bulk taxes
        return $this->taxRepository->createBulk($taxesWithOwnership);
    }

    /**
     * Update multiple tax groups in bulk.
     */
    public function updateBulkTaxGroups(array $groupsData): bool
    {
        $success = true;

        foreach ($groupsData as $groupData) {
            $groupId = $groupData['id'];
            unset($groupData['id']);

            if (!$this->taxGroupRepository->update($groupId, $groupData)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Delete multiple taxes in bulk with ownership validation.
     */
    public function deleteBulkTaxes(string $ownerType, ?string $ownerId, array $taxIds): bool
    {
        $success = true;

        foreach ($taxIds as $taxId) {
            if (!$this->deleteTax($ownerType, $ownerId, $taxId)) {
                $success = false;
            }
        }

        return $success;
    }

    // ===== ADVANCED QUERYING & REPORTING =====

    /**
     * Get all taxes by owner type (admin function).
     */
    public function getAllTaxesByOwnerType(string $ownerType): Collection
    {
        return $this->taxRepository->findByOwnerType($ownerType);
    }

    /**
     * Get tax statistics for an owner.
     */
    public function getTaxStatistics(string $ownerType, ?string $ownerId = null): array
    {
        $taxes = $this->getTaxes($ownerType, $ownerId);
        $taxGroups = $this->getTaxGroups($ownerType, $ownerId);

        $activeTaxes = $taxes->where('is_active', true);
        $activeGroups = $taxGroups->where('is_active', true);

        // Count rates across all groups
        $totalRates = 0;
        $activeRates = 0;

        foreach ($taxGroups as $group) {
            $rates = $this->getTaxRatesByGroup($group->id);
            $totalRates += $rates->count();
            $activeRates += $this->getActiveTaxRatesByGroup($group->id)->count();
        }

        return [
            'total_taxes' => $taxes->count(),
            'active_taxes' => $activeTaxes->count(),
            'total_groups' => $taxGroups->count(),
            'active_groups' => $activeGroups->count(),
            'total_rates' => $totalRates,
            'active_rates' => $activeRates,
        ];
    }

    /**
     * Get tax hierarchy for an owner (admin function).
     */
    public function getTaxHierarchy(string $ownerType, ?string $ownerId = null): array
    {
        $taxGroups = $this->getTaxGroups($ownerType, $ownerId);
        $hierarchy = [];

        foreach ($taxGroups as $group) {
            $rates = $this->getTaxRatesByGroup($group->id);
            $assignments = $this->getAssignedEntities($group->id);

            $hierarchy[] = [
                'group' => $group,
                'rates' => $rates,
                'assignments' => $assignments,
                'taxes_count' => $rates->unique('tax_id')->count(),
                'assignments_count' => $assignments->count(),
            ];
        }

        return $hierarchy;
    }

    /**
     * Search taxes by query string.
     */
    public function searchTaxes(string $query, string $ownerType, ?string $ownerId = null): Collection
    {
        // This is a simplified search - in real implementation, you might want to use
        // database full-text search or Elasticsearch
        return $this->taxRepository->findByOwnerType($ownerType, $ownerId)
            ->filter(function ($tax) use ($query) {
                return str_contains(strtolower($tax->name), strtolower($query)) ||
                    str_contains(strtolower($tax->description ?? ''), strtolower($query)) ||
                    str_contains(strtolower($tax->slug), strtolower($query));
            });
    }

    // ===== PERMISSION MANAGEMENT =====

    /**
     * Check if user can manage a specific tax group.
     */
    public function canManageTaxGroup(string $userId, string $userType, string $ownerType, ?string $ownerId, string $groupId): bool
    {
        // Placeholder permission logic - implement based on your auth system
        return $this->checkPermission($userId, $userType, $ownerType, $ownerId, $groupId);
    }

    /**
     * Check if user can manage tax assignments.
     */
    public function canManageTaxAssignment(string $userId, string $userType, string $taxGroupId): bool
    {
        // Get tax group to determine ownership
        $group = $this->getTaxGroupById($taxGroupId);

        if (!$group) {
            return false;
        }

        // Check permission based on group ownership
        return $this->canManageTaxGroup($userId, $userType, $group->owner_type, $group->owner_id, $taxGroupId);
    }

    /**
     * Validate tax group ownership dynamically.
     */
    public function validateTaxGroupOwnership(string $groupId, ?string $ownerId, string $ownerType): bool
    {
        $group = $this->taxGroupRepository->findById($groupId);

        if (!$group) {
            return false;
        }

        if ($group->owner_type !== $ownerType) {
            return false;
        }

        // For system groups, owner_id must be null
        if ($ownerType === 'system') {
            return $group->owner_id === null;
        }

        // For other types, owner_id must match
        return $group->owner_id === $ownerId;
    }
}
