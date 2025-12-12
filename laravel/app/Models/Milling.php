<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milling extends Model
{
    protected $fillable = [
        'name',
        'code',

    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
