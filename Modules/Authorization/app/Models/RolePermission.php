<?php

namespace Modules\Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Authorization\Database\Factories\RolePermissionFactory;

class RolePermission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'role_permissions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    /**
     * Get the role that owns the role permission.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the permission that owns the role permission.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    // protected static function newFactory(): RolePermissionFactory
    // {
    //     // return RolePermissionFactory::new();
    // }
}
