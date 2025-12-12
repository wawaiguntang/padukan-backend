<?php

namespace Modules\Tax\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tax';
    protected $table = 'taxes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the rates for the tax.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * Get the groups that contain this tax through rates.
     */
    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(TaxGroup::class, TaxRate::class, 'tax_id', 'id', 'id', 'tax_group_id');
    }

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
