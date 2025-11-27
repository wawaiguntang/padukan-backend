<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Driver Profile Model
 *
 * Represents driver-specific profile information
 */
class DriverProfile extends Model
{
    protected $connection = 'profile';

    protected $fillable = [
        'profile_id',
        'is_verified',
        'verification_status',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verification_status' => 'string',
    ];

    /**
     * Get the profile that owns the driver profile
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * Get the vehicles for the driver profile
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(DriverVehicle::class);
    }

    /**
     * Get the documents for the driver profile
     */
    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }
}
