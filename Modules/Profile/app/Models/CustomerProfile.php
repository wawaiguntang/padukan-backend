<?php

namespace Modules\Profile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Modules\Profile\Database\Factories\CustomerProfileFactory;

class CustomerProfile extends Model
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
    ];

    /**
     * Get the profile that owns the customer profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the documents for the customer profile.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    // protected static function newFactory(): CustomerProfileFactory
    // {
    //     // return CustomerProfileFactory::new();
    // }
}
