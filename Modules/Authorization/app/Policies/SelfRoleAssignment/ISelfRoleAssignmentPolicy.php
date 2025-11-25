<?php

namespace Modules\Authorization\Policies\SelfRoleAssignment;

interface ISelfRoleAssignmentPolicy
{
    /**
     * Evaluate if user can assign a specific role to themselves
     */
    public function evaluate(string $userId, string $roleSlug): bool;
}