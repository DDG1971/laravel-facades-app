<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;


class OrderItem extends Model
{
    use HasFactory;

 protected $fillable = [
     'order_id',
     'facade_type_id',
     'thickness_id',
     'height',
     'width',
     'square_meters',
     'quantity',
     'notes',
     'attachment_path',
     'unit_price',
     'item_total',
     'date_created',
     'coating_mode',
     'drilling_id',
     ];
 protected $casts = [
     'date_created' => 'date',
     ];
 // Связь с заказом
public function order()
{
    return $this->belongsTo(Order::class);
}
// поменял в миграции bool на tinyInteger(методы возвращают bool, потому что они проверяют конкретное состояние)
public function isDoubleSided(): bool
{
    return $this->coating_mode == 1;
}
public function isPartialCoating(): bool
{
    return $this->coating_mode == 2;
}
// Связь с типом фасада
    public function facadeType()
    {
        return $this->belongsTo(FacadeType::class);
    }
// Связь с толщиной
public function thickness()
{
    return $this->belongsTo(Thickness::class);
}
public function milling()
    {
        return $this->belongsTo(Milling::class);
    }
// расчет квадратуры позиции
    protected static function booted()
    {
        static::saving(function ($item) {
            // 1. Считаем квадраты (как и было)
            if ($item->height && $item->width && $item->quantity) {
                $item->square_meters = ($item->height * $item->width / 1_000_000) * $item->quantity;
            }

            // 2. ФИКСИРУЕМ ЦЕНУ И ИТОГО
            // Определяем группу цен клиента (дилер, розница и т.д.)
            $priceGroup = $item->order->price_group ?? 'retail'; //  $priceGroup = $item->order->customer->price_group ?? 'retail';

            // Вызываем расчет цены с учетом группы
            $total = $item->calculatePrice($priceGroup);

            // Записываем результаты в базу, чтобы они там "застыли"
            $item->item_total = $total;

            // Цена за единицу (делим общий итог строки на количество)
            $item->unit_price = ($item->quantity > 0) ? ($total / $item->quantity) : 0;
        });
    }
// Связь со сверлением
 public function drilling()
 {
     return $this->belongsTo(Drilling::class);
 }

// Логика добавки +4 мм
 public function needsSawAddition(): bool
 {
     // 1. Подготовка данных (чистим и приводим к нижнему регистру)
     $facadeName = strtolower(trim($this->facadeType->name_en ?? ''));
     $orderMillingName = strtolower(trim($this->order->milling->name_en ?? ''));

     $excluded = array_map('strtolower', config('facade.exclude_from_saw', []));
     $noAddition = array_map('strtolower', config('facade.no_addition', []));

     // --- ПРИОРИТЕТ 1: КОНКРЕТНАЯ СТРОКА (ПОЗИЦИЯ) ---

     // Если ЭТОТ фасад в списке исключенных (совсем не пилим)
     if (in_array($facadeName, $excluded, true)) {
         return false;
     }

     // Если ЭТОТ фасад в списке "без +4" (Bravo, Lhandle)
     if (in_array($facadeName, $noAddition, true)) {
         return false;
     }

     // Если ЭТОТ фасад НЕ в списках исключений (значит, он "плюсовой"),
     // то нам ПЛЕВАТЬ, что там в заголовке заказа. Плюсуем +4.
     if (!empty($facadeName) && !in_array($facadeName, $noAddition, true) && !in_array($facadeName, $excluded, true)) {
         return true;
     }

     // --- ПРИОРИТЕТ 2: ЗАГОЛОВОК ЗАКАЗА (MILLING) ---
     // (Сработает только если тип фасада в строке какой-то нейтральный/пустой)

     if (in_array($orderMillingName, $excluded, true) || in_array($orderMillingName, $noAddition, true)) {
         return false;
     }

     // По умолчанию для всего остального
     return true;
 }

    public function calculatePrice(string $priceGroup = 'retail'): float
    {
        // 1. Использую уже готовый метод для получения ставки (за м.п. или м2)
        $pricePerUnit = $this->getRate($priceGroup);

        // Выясняем единицу измерения (нужно для логики количества и допов)
        $millingBase = $this->order->milling?->getBasePriceFor($priceGroup) ?? 0;
        $facadePricing = $this->facadeType?->resolvePricing($millingBase, 'm2')
            ?? ['base' => $millingBase, 'unit' => 'm2'];
        $unit = $facadePricing['unit'];

        // 2. Определение "количества"
        if ($unit === 'lm') {
            $quantityAmount = ($this->height / 1000) * $this->quantity;
        } elseif ($unit === 'piece') {
            $quantityAmount = $this->quantity;
        } else {
            $quantityAmount = ($this->height * $this->width / 1_000_000) * $this->quantity;
        }

        $total = $pricePerUnit * $quantityAmount;

        // 3. Расчет допов только для листового материала (м2)
        if ($unit === 'm2') {
            $area = ($this->height * $this->width / 1_000_000) * $this->quantity;

            // Двусторонняя покраска
            if ($this->isDoubleSided()) {
                $basePaint = 20;
                $coatingExtra = 0;
                if ($this->order->coatingType) {
                    $coatingExtra = match(strtolower($this->order->coatingType->name)) {
                        'supermat', 'супермат' => 5,
                        'supermat+varnish', 'супермат+лак' => 10,
                        'supermat+varnish10%', 'супермат+лак10%' => 10,
                        'glossy', 'глянец' => 20,
                        default => 0
                    };
                }
                $total += ($area * ($basePaint + $coatingExtra));
            }

            // Сверловка — ТЕПЕРЬ ЧЕРЕЗ ЕДИНЫЙ МЕТОД
            if ($this->drilling) {
                $unitPrice = $this->drilling->price ?? 0;
                $count = $this->getDrillingCount(); // Вызываю  новый метод
                $total += ($unitPrice * $count * $this->quantity);
            }
        }

        return (float) $total;
    }


    public function getDrillingCount(): int
    {
        if (!$this->drilling) {
            return 0;
        }

        $drillingName = $this->drilling->name_en;
        // Определяем, по какой стороне считать петли (ширина или высота)
        $sizeForCalc = ($drillingName === 'hinges_width') ? $this->width : $this->height;

        // Если это не петли, а другой вид сверловки (например, 1 отверстие)
        if (!in_array($drillingName, ['hinges_height', 'hinges_width'])) {
            return 1;
        }

        //  сетка стандартов присадки
        return match (true) {
            $sizeForCalc < 1000 => 2,
            $sizeForCalc < 1500 => 3,
            $sizeForCalc < 2100 => 4,
            $sizeForCalc < 2500 => 5,
            default => 6,
        };
    }
    /**
     * Получение финальной ставки (цены за ед. изм.)
     */
    public function getRate(string $priceGroup = 'retail'): float
    {   //dd($priceGroup);
        $millingBase = $this->order->milling?->getBasePriceFor($priceGroup) ?? 0;
        $facadePricing = $this->facadeType?->resolvePricing($millingBase, 'm2', $priceGroup)
            ?? ['base' => $millingBase, 'unit' => 'm2'];

        $rate = (float) $facadePricing['base'];
        $unit = $facadePricing['unit'];
        $facadeName = $this->facadeType?->name_en;

        // Список исключений (для них наценка за покрытие всегда 0)
        $noCoatingExtra = [
            'PlugPVC',
            'CornerPVC_outer',
            'CornerPVC_inside'
        ];

        // Если это НЕ исключение и есть выбранное покрытие
        if (!in_array($facadeName, $noCoatingExtra) && $this->order->coatingType) {
            $cName = strtolower($this->order->coatingType->name);

            if ($unit === 'lm') {
                if (str_contains($cName, 'glossy')) {
                    $rate += 10;
                } elseif ($cName !== 'matte') {
                    $rate += 5;
                }
            } else {
                // Для обычных фасадов (m2) и других штучных изделий (piece)
                $rate += ($this->order->coatingType->price ?? 0);
                // Прибавляем цену за толщину ТОЛЬКО если это НЕ группа 'coloring'
                if ($priceGroup !== 'coloring') {
                    $rate += ($this->thickness?->price ?? 0);
                }
            }
            if ($this->coating_mode == 2) {
                $rate += 5; // Если "Частич", ставка растет на 5
            }
        }

        return $rate;
    }


}
