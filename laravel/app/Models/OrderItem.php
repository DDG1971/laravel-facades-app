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
        'milling_id',
        'coating_type_id',
        'color_catalog_id',
        'color_code_id',
        'drilling_id'       =>  null,
        'material'          => 'MDF',
        'thickness'         => '19',
        'height',
        'width',
        'square_meters',
        'quantity',
        'notes',
        'attachment_path',
        'unit_price',
        'total_price',
        'payment_status',
        'prepayment',
        'paid_amount',
        'status_id',
        'date_status',
        'date_created',
        'double_sided_coating',
    ];

    //  Связь с заказом


    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    //  Связь с типом фасада
    public function facadeType()
    {
        return $this->belongsTo(FacadeType::class);
    }

    //  Связь с фрезеровкой
    public function milling()
    {
        return $this->belongsTo(Milling::class);
    }

    //  Связь с каталогом цветов
    public function colorCatalog()
    {
        return $this->belongsTo(ColorCatalog::class);
    }

    //  Связь с кодом цвета
    public function colorCode()
    {
        return $this->belongsTo(ColorCode::class);
    }

    //  Статус позиции
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    //  Связь с сверлением
    public function drilling()
    {
        return $this->belongsTo(Drilling::class);
    }
    public function isDoubleSided(): bool
    {
        return (bool) $this->double_sided_coating;
    }
    public function coatingType()
    {
        return $this->belongsTo(CoatingType::class);
    }


}
