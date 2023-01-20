<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCoin extends Model
{
    use HasFactory;

    public function pricerange()
    {
        return $this->hasOne(PriceRange::class, 'id', 'package_id');
    }
}
