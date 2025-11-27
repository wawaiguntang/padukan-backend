<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Driver Vehicle Model
 *
 * Represents vehicles owned by drivers
 */
class DriverVehicle extends Model
{
    protected $connection = 'profile';

    protected $fillable = [
        'driver_profile_id',
        'type',
        'brand',
        'model',
        'year',
        'color',
        'license_plate',
        'is_verified',
        'verification_status',
    ];

    protected $casts = [
        'year' => 'integer',
        'is_verified' => 'boolean',
        'verification_status' => 'string',
    ];

    /**
     * Get the driver profile that owns the vehicle
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }
}
