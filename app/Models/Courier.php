<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'logo',
        'type',
        'min_charge',
        'max_charge',
        'delivery_charge',
        'base_url',
        'test_base_url',
        'client_id',
        'client_secret',
        'test_client_id',
        'test_client_secret',
        'client_email',
        'client_password',
        'grant_type',
        'store_id',
        'is_live',
        'is_active',
    ];

    protected $casts = [
        'min_charge' => 'decimal:2',
        'max_charge' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'is_live' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the logo URL attribute
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Scope to get only active couriers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get couriers by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
