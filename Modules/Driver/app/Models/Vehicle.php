<?php

namespace Modules\Driver\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * Driver Vehicle Model
 *
 * Represents vehicles owned by drivers
 */
class Vehicle extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'driver';

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

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => \Modules\Driver\Enums\VehicleTypeEnum::class,
            'year' => 'integer',
            'is_verified' => 'boolean',
            'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::class,
        ];
    }

    /**
     * Get the driver profile that owns the vehicle
     */
    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'driver_profile_id');
    }

    /**
     * Get the documents for the vehicle
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
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
