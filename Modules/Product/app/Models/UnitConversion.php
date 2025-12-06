<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class UnitConversion extends Model
{
    use HasFactory;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'from_unit',
        'to_unit',
        'multiply_factor',
        'metadata',
        'parent_conversion_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'multiply_factor' => 'decimal:6',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the parent conversion.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(UnitConversion::class, 'parent_conversion_id');
    }

    /**
     * Get the child conversions.
     */
    public function children(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'parent_conversion_id');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
