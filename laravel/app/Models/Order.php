<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use App\Models\Box;
/**
 * @property int $status_id
 * @property int $customer_id
 * @property string $material
 * // и другие поля таблицы orders
 */


class Order extends Model

{

    protected $fillable = [
        'customer_id',
        'user_id',
        'status_id',
        'queue_number',          // внутренний счётчик
        'client_order_number',   // номер клиента
        'material',
        'total_price',
        'square_meters',
        'coating_type_id',
        'color_catalog_id',
        'color_code_id',
        'milling_id',
        'paint_shop_id',
        'notes',
        'attachment_path',
        'date_received',
        'date_status',
        'payment_status',
        'paid_amount',
        'price_group',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coatingType()
    {
        return $this->belongsTo(CoatingType::class);
    }

    public function colorCatalog()
    {
        return $this->belongsTo(ColorCatalog::class);
    }

    public function colorCode()
    {
        return $this->belongsTo(ColorCode::class);
    }

    public function milling()
    {
        return $this->belongsTo(Milling::class);
    }
    public function paintShop()
    {
        return $this->belongsTo(PaintShop::class, 'paint_shop_id');
    }
    public function getTotalSquareAttribute()
    {
        return $this->items->sum(function($item) {

           return ($item->height * $item->width / 1_000_000) * $item->quantity;
        });
    }
    protected $casts = [
        'date_created' => 'date',
        'date_status'  => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getPriceGroup(): string
    {
        // Предположим, у клиента есть поле price_group (retail, dealer, private)
        return $this->customer->price_group ?? 'retail';
    }

    public function calculateTotal(?string $priceGroup = null): float
    {
        // 1. Вычисляем рабочую группу (если пришел null, берем из базы)
        $group = $priceGroup ?: ($this->price_group ?: $this->getPriceGroup());

        // 2. ВАЖНО: передаем $group в use и вызываем метод тоже с $group
        return (float) $this->items->sum(function($item) use ($group) {
            return $item->calculatePrice($group);
        });
        }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Вычисляет остаток, который клиент должен доплатить.
     * Использование в коде: $order->debt_amount
     */

    public function getDebtAmountAttribute()
    {
        // Берем группу прямо из этого заказа
        $group = $this->price_group ?? 'retail';

        // Аналогично: считаем по формуле, пока не оплачено полностью
        $total = ($this->payment_status === 'paid' && $this->total_price > 0)
            ? $this->total_price
            : $this->calculateTotal($group);

        return max(0, $total - ($this->paid_amount ?? 0));
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }




}
