<?php

namespace Modules\Driver\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Driver Status Model
 *
 * Represents the status and location of drivers
 */
class DriverAvailabilityStatus extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'driver';

    /**
     * The table associated with the model.
     */
    protected $table = 'driver_statuses';

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
        'profile_id',
        'online_status',
        'operational_status',
        'active_services',
        'vehicle_id',
        'latitude',
        'longitude',
        'last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'online_status' => \Modules\Driver\Enums\OnlineStatusEnum::class,
            'operational_status' => \Modules\Driver\Enums\OperationalStatusEnum::class,
            'active_services' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'last_updated_at' => 'datetime',
        ];
    }

    /**
     * Get the driver profile that owns the availability status
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the vehicle associated with this driver status
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
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
