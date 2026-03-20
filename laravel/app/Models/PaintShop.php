<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaintShop extends Model
{

    protected $fillable = ['name', 'full_name'];

    /**
     * Один цех имеет много заказов
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
