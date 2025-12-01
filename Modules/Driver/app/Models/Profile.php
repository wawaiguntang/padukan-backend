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
        'verified_services',
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
            'verified_services' => 'array',
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
     * Check if the driver can provide a specific service based on verified vehicles
     */
    public function canProvideService(\App\Enums\ServiceTypeEnum $service): bool
    {
        return in_array($service->value, $this->getAvailableServices());
    }

    /**
     * Check if the driver is verified for a specific service
     */
    public function isVerifiedForService(\App\Enums\ServiceTypeEnum $service): bool
    {
        return in_array($service->value, $this->verified_services ?? []);
    }

    /**
     * Check if the driver has available services
     */
    public function hasAvailableServices(): bool
    {
        return !empty($this->getAvailableServices());
    }

    /**
     * Get the driver's available services based on verified vehicle types
     */
    public function getAvailableServices(): array
    {
        $services = [];

        // Get verified vehicles
        $verifiedVehicles = $this->vehicles()->where('is_verified', true)->get();

        foreach ($verifiedVehicles as $vehicle) {
            switch ($vehicle->type) {
                case \Modules\Driver\Enums\VehicleTypeEnum::MOTORCYCLE:
                    $services = array_merge($services, [
                        \App\Enums\ServiceTypeEnum::RIDE->value,
                        \App\Enums\ServiceTypeEnum::FOOD->value,
                        \App\Enums\ServiceTypeEnum::SEND->value,
                        \App\Enums\ServiceTypeEnum::MART->value
                    ]);
                    break;
                case \Modules\Driver\Enums\VehicleTypeEnum::CAR:
                    $services = array_merge($services, [
                        \App\Enums\ServiceTypeEnum::CAR->value,
                        \App\Enums\ServiceTypeEnum::SEND->value
                    ]);
                    break;
            }
        }

        return array_unique($services);
    }

    /**
     * Get the driver's verified services
     */
    public function getVerifiedServices(): array
    {
        return $this->verified_services ?? [];
    }

    /**
     * Get the current active service from driver status
     */
    public function getCurrentActiveService(): ?\App\Enums\ServiceTypeEnum
    {
        return $this->driverStatus?->active_service;
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
