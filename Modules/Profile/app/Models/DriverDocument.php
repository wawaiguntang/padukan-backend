<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Driver Document Model
 *
 * Represents documents uploaded by drivers
 */
class DriverDocument extends Model
{
    protected $connection = 'profile';

    protected $fillable = [
        'driver_profile_id',
        'type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'meta',
        'expiry_date',
        'is_verified',
        'verification_status',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'meta' => 'array',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
        'verification_status' => 'string',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the driver profile that owns the document
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(DriverProfile::class);
    }
}
