<?php

namespace App\Models;

use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Model;
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
        // Если группа не передана явно, берем ту, что привязана к клиенту
        $group = $priceGroup ?: $this->getPriceGroup();

        return $this->items->sum(fn($item) => $item->calculatePrice($group));
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
        // Если в базе 0, берем расчетную цену, иначе сохраненную
        $total = $this->total_price > 0 ? $this->total_price : $this->calculateTotal('retail');
        $debt = $total - $this->paid_amount;
        return $debt > 0 ? $debt : 0;
    }





}
