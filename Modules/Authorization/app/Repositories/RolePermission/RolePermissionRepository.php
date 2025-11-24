<?php

namespace Modules\Authorization\Repositories\RolePermission;

use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\RolePermission;

/**
 * Role Permission Repository Implementation
 *
 * This class handles all role-permission relationship database operations
 * for the authorization module.
 */
class RolePermissionRepository implements IRolePermissionRepository
{
    /**
     * The RolePermission model instance
     *
     * @var RolePermission
     */
    protected RolePermission $model;

    /**
     * Constructor
     *
     * @param RolePermission $model The RolePermission model instance
     */
    public function __construct(RolePermission $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function findByIds(int $roleId, int $permissionId): ?RolePermission
    {
        return $this->model
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->first();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): RolePermission
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $roleId, int $permissionId): bool
    {
        return $this->model
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->delete() > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(int $roleId, int $permissionId): bool
    {
        return $this->model
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function getPermissionsByRole(int $roleId)
    {
        return $this->model
            ->with('permission')
            ->where('role_id', $roleId)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getRolesByPermission(int $permissionId)
    {
        return $this->model
            ->with('role')
            ->where('permission_id', $permissionId)
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function bulkCreate(array $relationships): bool
    {
        try {
            DB::beginTransaction();

            foreach ($relationships as $relationship) {
                // Skip if relationship already exists
                if (!$this->exists($relationship['role_id'], $relationship['permission_id'])) {
                    $this->create($relationship);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function bulkDelete(array $relationships): bool
    {
        try {
            DB::beginTransaction();

            foreach ($relationships as $relationship) {
                $this->delete($relationship['role_id'], $relationship['permission_id']);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}