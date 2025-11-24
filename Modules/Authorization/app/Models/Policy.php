<?php

namespace Modules\Authorization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Authorization\Database\Factories\PolicyFactory;

class Policy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'resource',
        'actions',
        'scope',
        'group',
        'is_active',
        'priority',
        'conditions',
        'module',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'actions' => 'array',
        'conditions' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Scope a query to only include active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by group and priority.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('group')->orderBy('priority', 'desc');
    }

    /**
     * Scope a query to filter by scope.
     */
    public function scopeInScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Check if the policy has a specific action.
     */
    public function hasAction($action)
    {
        return in_array($action, $this->actions ?? []);
    }

    // protected static function newFactory(): PolicyFactory
    // {
    //     // return PolicyFactory::new();
    // }
}
