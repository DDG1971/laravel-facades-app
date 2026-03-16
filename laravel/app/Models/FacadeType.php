<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FacadeType extends Model
{
    protected $fillable = [
        'name_en',
        'name_ru',
        'pricing_mode',
        'pricing_value',
        'unit_mode',
    ];

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute(): ?string
    {
        $value = $this->name_ru ?? $this->name_en;
        $value = $value ? trim($value) : null;

        return $value !== '' ? $value : null;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function resolvePricing(float $millingBase, string $millingUnit, string $priceGroup = 'retail'): array
    {
        $base = $millingBase; // По умолчанию берем цену из шапки заказа (Genoa)

        switch ($this->pricing_mode) {
            case 'set_base':
                $base = (float) ($this->pricing_value ?? 0);
                break;

            case 'percent_add':
                $percent = (float) ($this->pricing_value ?? 0);
                $base = $millingBase * (1 + $percent / 100);
                break;

            case 'inherit':
                // Пытаемся найти фрезеровку, имя которой совпадает с именем фасада (например, "Bravo")
                $linkedMilling = \App\Models\Milling::where('name_en', $this->name_en)->first();

                if ($linkedMilling) {
                    // Если нашли свою фрезеровку — берем ЕЁ цену для текущей группы (dealer/retail)
                    $base = $linkedMilling->getBasePriceFor($priceGroup);
                } else {
                    // Если своей фрезеровки нет — оставляем цену из шапки заказа
                    $base = $millingBase;
                }
                break;

            default:
                $base = $millingBase;
                break;
        }

        $unit = ($this->unit_mode === 'inherit') ? $millingUnit : $this->unit_mode;

        return [
            'base' => $base,
            'unit' => $unit,
        ];
    }
    public function setNameRuAttribute($value)
    {
        $this->attributes['name_ru'] = $value ? trim($value) : null;
    }
    public function setNameEnAttribute($value)
    {
        $this->attributes['name_en'] = $value ? trim($value) : null;
    }
}
