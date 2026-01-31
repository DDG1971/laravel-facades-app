<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milling extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price_retail',
        'price_dealer',
        'price_private',
        'name_en',
    ];

    protected $casts = [
        'price_retail' => 'decimal:2',
        'price_dealer' => 'decimal:2',
        'price_private' => 'decimal:2',
    ];

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute(): ?string
    {
        $value = $this->name_en ?? $this->name;
        $value = $value ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    public function getBasePriceFor(string $priceGroup): float
    {
        return match ($priceGroup) {
            'retail' => (float) ($this->price_retail ?? 0),
            'dealer' => (float) ($this->price_dealer ?? 0),
            'private' => (float) ($this->price_private ?? 0),
        };
    }

    public function hasPriceFor(string $priceGroup): bool
    {
        return match ($priceGroup) {
            'retail' => $this->price_retail !== null,
            'dealer' => $this->price_dealer !== null,
            'private' => $this->price_private !== null,
        };
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value ? trim($value) : null;
    }
    public function setNameEnAttribute($value)
    {
        $this->attributes['name_en'] = $value ? trim($value) : null;
    }
}

