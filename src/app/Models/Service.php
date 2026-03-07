<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'price',
        'duration_min',
    ];

    function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'reservation_service')
            ->withPivot('qty', 'unit_price');
    }

}
