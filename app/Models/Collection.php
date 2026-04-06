<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'waste_id',
        'collector_id',
        'status',
        'collected_at',
        'notes',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    public function waste()
    {
        return $this->belongsTo(Waste::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }
}
