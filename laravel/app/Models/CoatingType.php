<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoatingType extends Model
{
    protected $fillable = [
        'name',
        'label',
        'description',
        'price'
        ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
