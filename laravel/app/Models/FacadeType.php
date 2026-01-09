<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacadeType extends Model
{

    protected $fillable = ['name_en', 'name_ru'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getDisplayNameAttribute()
    {
        return $this->name_ru ?? $this->name_en ?? $this->name;
    }

}
