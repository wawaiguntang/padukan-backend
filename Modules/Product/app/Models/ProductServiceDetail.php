<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductServiceDetail extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'service_id';

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
        'service_id',
        'product_id',
        'variant_id',
        'duration_minutes',
        'staff_required',
        'min_participants',
        'max_participants',
        'optional_extras',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'staff_required' => 'integer',
            'min_participants' => 'integer',
            'max_participants' => 'integer',
            'optional_extras' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the product that owns the service detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant that owns the service detail.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->service_id)) {
                $model->service_id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
