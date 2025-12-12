<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drilling extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ru'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
