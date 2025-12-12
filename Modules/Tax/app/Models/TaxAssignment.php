<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TaxAssignment extends Model
{
    use HasFactory;

    protected $connection = 'tax';
    protected $table = 'tax_assignments';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tax_group_id',
        'assignable_type',
        'assignable_id',
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
     * Get the tax group that owns the assignment.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id');
    }

    /**
     * Get the assignable entity (polymorphic relationship).
     * This can be any entity type: region, category, product, branch, franchise, etc.
     */
    public function assignable()
    {
        return $this->morphTo();
    }
}
