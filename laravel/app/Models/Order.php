<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'status_id',
        'order_number',
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
        'prepayment',
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
}
