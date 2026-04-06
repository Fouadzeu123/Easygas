<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    protected $fillable = [
        'user_id',
        'collector_id',
        'type',
        'quantity',
        'photo',
        'status',
        'latitude',
        'longitude',
        'description',
        'points_awarded',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }
}
