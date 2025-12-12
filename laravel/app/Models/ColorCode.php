<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorCode extends Model
{
    protected $fillable = [
        'code',
        'color_catalog_id',
    ];

    public function colorCatalog()
    {
        return $this->belongsTo(ColorCatalog::class);
    }
}
