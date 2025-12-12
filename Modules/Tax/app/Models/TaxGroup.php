<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TaxGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tax';
    protected $table = 'tax_groups';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the rates for the tax group.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * Get the assignments for the tax group.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TaxAssignment::class);
    }
}
