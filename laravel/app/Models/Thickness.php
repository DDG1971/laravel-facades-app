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
    public function scopeOrdered($query)
    {
        return $query->orderByRaw("
         CASE value
          WHEN 19 THEN 1
           WHEN 22 THEN 2
            WHEN 16 THEN 3
             WHEN 38 THEN 4
              ELSE 5
              END
              ")
            ->orderBy('value');
    }
}
