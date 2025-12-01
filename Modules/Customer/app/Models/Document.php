<?php

namespace Modules\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Modules\Profile\Database\Factories\CustomerDocumentFactory;

class Document extends Model
{
    use HasFactory;

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
            'type' => \Modules\Customer\Enums\DocumentTypeEnum::class,
            'meta' => 'array',
            'expiry_date' => 'date',
            'verification_status' => \Modules\Customer\Enums\VerificationStatusEnum::class,
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the customer profile that owns the document.
     */
    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    // protected static function newFactory(): CustomerDocumentFactory
    // {
    //     // return CustomerDocumentFactory::new();
    // }
}
