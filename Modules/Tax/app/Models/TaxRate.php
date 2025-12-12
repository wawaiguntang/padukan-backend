<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\Tax\Enums\TaxTypeEnum;

class TaxRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tax';
    protected $table = 'tax_rates';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'tax_group_id',
        'tax_id',
        'rate',
        'type',
        'is_inclusive',
        'priority',
        'based_on',
        'valid_from',
        'valid_until',
        'min_price',
        'max_price',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'type' => TaxTypeEnum::class,
            'is_inclusive' => 'boolean',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
        ];
    }

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
     * Get the tax group that owns the rate.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(TaxGroup::class, 'tax_group_id');
    }

    /**
     * Get the tax definition.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
