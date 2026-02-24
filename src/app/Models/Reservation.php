<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    protected $fillable = [
        'client_id',
        'designer_id',
        'requirement',
        'day',
        'start_at',
        'end_at',
        'status',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'day' => 'date',
        'cancelled_at' => 'datetime',
    ];

    // reservations.client_id -> users.id
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // reservations.designer_id -> designers.id
    public function designer(): BelongsTo
    {
        return $this->belongsTo(Designer::class, 'designer_id');
    }

    // reservations と services の多対多リレーション
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'reservation_service')
            ->withPivot('qty', 'unit_price');
    }

     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
