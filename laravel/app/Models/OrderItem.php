<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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
     'double_sided_coating',
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
// расчет квадратуры позиции
    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->height && $item->width && $item->quantity) {
                $item->square_meters = ($item->height * $item->width / 1_000_000) * $item->quantity;
            }
        });
    }
// Связь со сверлением
 public function drilling()
 {
     return $this->belongsTo(Drilling::class);
 }
 // Флаг двустороннего покрытия
public function isDoubleSided(): bool
{
    return (bool) $this->double_sided_coating;
}
// Логика добавки +4 мм
 public function needsSawAddition(): bool
 {
     $excludedFacades = config('facade.exclude_from_saw');
     $noAddition = config('facade.no_addition');
     // если фасад вообще не пилится
if (in_array($this->facadeType->name_en ?? '', $excludedFacades, true)) {
    return false;
}
// если фасад в списке "без +4"
 if (in_array($this->facadeType->name_en ?? '', $noAddition, true)) {
     return false;
 }
 // по умолчанию → прибавляем +4
return true;
 }

    public function calculatePrice(string $priceGroup = 'retail'): float
    {
        // 1. базовая цена от Milling
        $millingBase = $this->order->milling?->getBasePriceFor($priceGroup) ?? 0;
        $millingUnit = 'm2';

        // 2. фасад корректирует базовую цену
        $facadePricing = $this->facadeType?->resolvePricing($millingBase, $millingUnit)
            ?? ['base' => $millingBase, 'unit' => $millingUnit];
        $pricePerUnit = $facadePricing['base'];

        // 3. добавка за толщину
        $pricePerUnit += $this->thickness?->price ?? 0;

        // 4. добавка за покрытие (берётся из заказа)
        $pricePerUnit += $this->order->coatingType?->price ?? 0;

        //5 . площадь поз
        $area = ($this->height * $this->width / 1_000_000) * $this->quantity;

        // 6. отдельный расчёт за двухсторонний окрас
         $doubleSidedCost = 0;
         if ($this->isDoubleSided()) {
             $basePaint = 20;
             $coatingExtra = 0;

             if ($this->order->coatingType) {
                 switch ($this->order->coatingType->name) {
                     case 'supermat': $coatingExtra = 5;
                     break;
                     case 'supermat+lacquer': $coatingExtra = 10;
                     break;
                     case 'glossy': $coatingExtra = 20;
                     break;
                 }
             }
             $doubleSidedCost = $area * ($basePaint + $coatingExtra);
         }
         // итоговая цена = цена за м² * площадь + доп. окрас

         return ($pricePerUnit * $area) + $doubleSidedCost;
    }
}
