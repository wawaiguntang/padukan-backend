<?php

namespace Modules\Merchant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MerchantSetting extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'merchant';

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
        'delivery_enabled',
        'delivery_radius_km',
        'minimum_order_amount',
        'auto_accept_orders',
        'preparation_time_minutes',
        'notifications_enabled',
        'use_inventory',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'delivery_enabled' => 'boolean',
            'delivery_radius_km' => 'integer',
            'minimum_order_amount' => 'decimal:2',
            'auto_accept_orders' => 'boolean',
            'preparation_time_minutes' => 'integer',
            'notifications_enabled' => 'boolean',
            'use_inventory' => 'boolean',
        ];
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

    /**
     * Get the merchant that owns the settings.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
