<?php

namespace Modules\Driver\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * Driver Document Model
 *
 * Represents documents uploaded by drivers
 */
class Document extends Model
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
        'documentable_id',
        'documentable_type',
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

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => \Modules\Driver\Enums\DocumentTypeEnum::class,
            'meta' => 'array',
            'expiry_date' => 'date',
            'verification_status' => \Modules\Driver\Enums\VerificationStatusEnum::class,
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the parent documentable model (profile or vehicle)
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
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
