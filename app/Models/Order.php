<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'collector_id',
        'quantity',
        'price',
        'status',
        'latitude',
        'longitude',
        'address',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}
