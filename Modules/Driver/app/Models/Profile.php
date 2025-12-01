<?php

namespace Modules\Driver\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

/**
 * Driver Profile Model
 *
 * Represents driver-specific profile information
 */
class Profile extends Model
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
        'user_id',
        'first_name',
        'last_name',
        'avatar',
        'gender',
        'language',
        'is_verified',
        'verification_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::class,
            'gender' => \Modules\Driver\Enums\GenderEnum::class,
        ];
    }

    /**
     * Get the vehicles for the driver profile
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'driver_profile_id');
    }

    /**
     * Get the documents for the driver profile
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get the driver status for the driver profile
     */
    public function driverStatus(): HasOne
    {
        return $this->hasOne(DriverAvailabilityStatus::class, 'profile_id');
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
