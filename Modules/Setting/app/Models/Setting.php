<?php

namespace Modules\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     */
    protected $connection = 'setting';

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
        'key',
        'value',
        'type',
        'group',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the typed value based on the setting type.
     */
    public function getTypedValue()
    {
        return match ($this->type) {
            'string' => $this->value,
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode($this->value, true) ?? [],
            'object' => json_decode($this->value, true) ?? new \stdClass(),
            default => $this->value,
        };
    }

    /**
     * Set the value with automatic type detection.
     */
    public function setTypedValue($value): void
    {
        $this->type = $this->detectType($value);
        $this->value = $this->serializeValue($value);
    }

    /**
     * Detect the type of the value.
     */
    private function detectType($value): string
    {
        return match (true) {
            is_string($value) => 'string',
            is_int($value) => 'integer',
            is_bool($value) => 'boolean',
            is_array($value) => 'array',
            is_object($value) => 'object',
            default => 'string',
        };
    }

    /**
     * Serialize the value for storage.
     */
    private function serializeValue($value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Check if setting is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope a query to only include active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to filter by key.
     */
    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
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
