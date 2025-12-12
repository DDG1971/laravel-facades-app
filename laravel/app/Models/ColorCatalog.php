<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorCatalog extends Model
{
    protected $fillable = [
        'name_en',

    ];

    public function colorCodes()
    {
        return $this->hasMany(OrderItem::class);
    }
}
