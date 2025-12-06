<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

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
        'product_id',
        'name',
        'sku',
        'barcode',
        'attribute_master_ids',
        'attribute_custom_ids',
        'unit',
        'conversion_id',
        'price',
        'has_expired',
        'metadata',
        'version',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'attribute_master_ids' => 'array',
            'attribute_custom_ids' => 'array',
            'price' => 'decimal:2',
            'has_expired' => 'boolean',
            'metadata' => 'array',
            'version' => 'integer',
        ];
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the unit conversion for the variant.
     */
    public function conversion(): BelongsTo
    {
        return $this->belongsTo(UnitConversion::class, 'conversion_id');
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
