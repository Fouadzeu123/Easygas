<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'gas_price_per_kg',
        'waste_reward_per_kg',
        'delivery_fee',
    ];
}
