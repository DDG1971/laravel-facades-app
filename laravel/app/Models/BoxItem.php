<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoxItem extends Model
{
    protected $fillable = ['box_id', 'order_item_id', 'quantity'];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
