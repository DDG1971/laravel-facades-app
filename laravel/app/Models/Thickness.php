<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thickness extends Model
{
    protected $fillable = [
        'value',
        'price',
        'label'
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
