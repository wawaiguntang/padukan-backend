<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Product\Enums\ProductStatusEnum;
use Modules\Product\Enums\ProductTypeEnum;

class Product extends Model
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
        'merchant_id',
        'category_id',
        'name',
        'slug',
        'description',
        'type',
        'status',
        'barcode',
        'sku',
        'base_unit',
        'price',
        'has_variant',
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
            'type' => ProductTypeEnum::class,
            'status' => ProductStatusEnum::class,
            'price' => 'decimal:2',
            'has_variant' => 'boolean',
            'has_expired' => 'boolean',
            'metadata' => 'array',
            'version' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the extras for the product.
     */
    public function extras(): HasMany
    {
        return $this->hasMany(ProductExtra::class);
    }

    /**
     * Get the service details for the product.
     */
    public function serviceDetails(): HasMany
    {
        return $this->hasMany(ProductServiceDetail::class);
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
